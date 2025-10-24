<?php
session_start();
include 'db.php';
if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] !=1){ header("Location:login.php"); exit();}
$admin_id = $_SESSION['user_id'];

// ---------- HANDLE AJAX ----------
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if($action=='send'){
        $customer_id=intval($_POST['customer_id']);
        $msg=trim($_POST['message']);
        if($msg!=''){
            $stmt=$conn->prepare("INSERT INTO messages(sender_id,receiver_id,message,sent_at,is_read) VALUES(?,?,?,?,0)");
            $stmt->bind_param("iiss",$admin_id,$customer_id,$msg,date("Y-m-d H:i:s"));
            $stmt->execute(); $stmt->close(); echo 'success'; exit;
        }
    }
    if($action=='update'){
        $msg_id=intval($_POST['msg_id']); $msg=trim($_POST['message']);
        $stmt=$conn->prepare("UPDATE messages SET message=? WHERE id=? AND sender_id=?");
        $stmt->bind_param("sii",$msg,$msg_id,$admin_id); $stmt->execute(); $stmt->close(); echo 'success'; exit;
    }
    if($action=='delete'){
        $msg_id=intval($_POST['msg_id']);
        $stmt=$conn->prepare("DELETE FROM messages WHERE id=? AND sender_id=?");
        $stmt->bind_param("ii",$msg_id,$admin_id); $stmt->execute(); $stmt->close(); echo 'success'; exit;
    }
}

// ---------- FETCH MESSAGES ----------
if(isset($_GET['action']) && $_GET['action']=='fetch' && isset($_GET['customer_id'])){
    $customer_id=intval($_GET['customer_id']);
    $stmt=$conn->prepare("SELECT * FROM messages WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) ORDER BY sent_at ASC");
    $stmt->bind_param("iiii",$customer_id,$admin_id,$admin_id,$customer_id);
    $stmt->execute(); $res=$stmt->get_result(); $data=$res->fetch_all(MYSQLI_ASSOC); $stmt->close();
    $conn->query("UPDATE messages SET is_read=1 WHERE sender_id=$customer_id AND receiver_id=$admin_id");
    echo json_encode($data); exit;
}

// ---------- FETCH CUSTOMERS ----------
$customers_res=$conn->query("SELECT user_id, full_name FROM users WHERE role_id=3");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Chat Panel</title>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* Scrollbar styling */
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-thumb { background-color: #2563eb; border-radius: 3px; }
</style>
</head>
<body class="bg-gray-100 p-5">

<h2 class="text-2xl font-bold mb-4">💬 Admin Chat Panel</h2>

<div class="flex gap-4 h-[70vh]">
  <!-- Customers List -->
  <div class="w-64 bg-white rounded-lg shadow p-3 overflow-y-auto">
    <?php if($customers_res && $customers_res->num_rows>0): while($c=$customers_res->fetch_assoc()): ?>
      <div data-id="<?=$c['user_id']?>" class="p-2 rounded cursor-pointer hover:bg-blue-500 hover:text-white mb-1"><?=$c['full_name']?></div>
    <?php endwhile; else: ?>
      <div>No customers yet</div>
    <?php endif; ?>
  </div>

  <!-- Chat Box -->
  <div class="flex-1 bg-white rounded-lg shadow flex flex-col">
    <div class="flex-1 overflow-y-auto p-4 flex flex-col gap-2" id="chatMessages"></div>
    <div class="flex p-3 border-t border-gray-200">
      <input type="text" id="msgInput" placeholder="Type a message..." class="flex-1 border rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <button id="sendBtn" class="ml-2 bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-full">Send</button>
    </div>
  </div>
</div>

<script>
let selectedCustomer=null;

// Select customer
$('[data-id]').click(function(){
    $('[data-id]').removeClass('bg-blue-500 text-white');
    $(this).addClass('bg-blue-500 text-white');
    selectedCustomer=$(this).data('id'); loadMessages();
});

// Load messages
function loadMessages(){
    if(!selectedCustomer) return;
    $.get('messages_customer.php',{action:'fetch',customer_id:selectedCustomer},function(data){
        let html='';
        JSON.parse(data).forEach(m=>{
            const isAdmin = m.sender_id==<?=$admin_id?>;
            const cls = isAdmin?'bg-blue-500 text-white self-end':'bg-gray-200 text-black self-start';
            const actionBtns = isAdmin?`
                <div class="flex flex-col ml-2">
                  <button class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs px-2 py-1 rounded mb-1" onclick="editMsg(${m.id},'${m.message.replace(/'/g,"\\'")}')">Edit</button>
                  <button class="bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded" onclick="deleteMsg(${m.id})">Delete</button>
                </div>`:'';
            html += `<div class="flex items-start ${isAdmin?'justify-end':'justify-start'} gap-2">
                        ${isAdmin?actionBtns:''}
                        <div class="p-2 rounded-xl max-w-[70%] ${cls}">${m.message}</div>
                        ${!isAdmin?actionBtns:''}
                     </div>`;
        });
        $('#chatMessages').html(html);
        $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
    });
}

// Send message
$('#sendBtn').click(function(){
    const msg=$('#msgInput').val().trim();
    if(!selectedCustomer || msg=='') return;
    $.post('messages_customer.php',{action:'send',customer_id:selectedCustomer,message:msg},function(res){
        if(res=='success'){$('#msgInput').val(''); loadMessages();}
    });
});

// Edit message
function editMsg(id,current){
    const newMsg=prompt("Edit message:",current);
    if(newMsg!==null && newMsg.trim()!==''){
        $.post('messages_customer.php',{action:'update',msg_id:id,message:newMsg},function(res){ if(res=='success') loadMessages(); });
    }
}

// Delete message
function deleteMsg(id){
    if(confirm("Are you sure you want to delete this message?")){
        $.post('messages_customer.php',{action:'delete',msg_id:id},function(res){ if(res=='success') loadMessages(); });
    }
}

setInterval(loadMessages,2000);
</script>

</body>
</html>
