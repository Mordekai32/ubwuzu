<?php
session_start();
include 'db.php';

// ✅ Check if admin is logged in
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['user_id']; // Admin ID

// ---------- SEND MESSAGE ----------
if (isset($_POST['action']) && $_POST['action'] == 'send') {
    $customer_id = intval($_POST['customer_id']);
    $msg = trim($_POST['message']);
    if ($msg != '') {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, sent_at, is_read) VALUES (?, ?, ?, NOW(), 0)");
        $stmt->bind_param("iis", $admin_id, $customer_id, $msg);
        $stmt->execute();
        $stmt->close();
        echo 'success';
    }
    exit;
}

// ---------- UPDATE MESSAGE ----------
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $msg_id = intval($_POST['msg_id']);
    $msg = trim($_POST['message']);
    $stmt = $conn->prepare("UPDATE messages SET message=? WHERE id=? AND sender_id=?");
    $stmt->bind_param("sii", $msg, $msg_id, $admin_id);
    $stmt->execute();
    $stmt->close();
    echo 'success';
    exit;
}

// ---------- DELETE MESSAGE ----------
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $msg_id = intval($_POST['msg_id']);
    $stmt = $conn->prepare("DELETE FROM messages WHERE id=? AND sender_id=?");
    $stmt->bind_param("ii", $msg_id, $admin_id);
    $stmt->execute();
    $stmt->close();
    echo 'success';
    exit;
}

// ---------- FETCH MESSAGES ----------
if (isset($_GET['action']) && $_GET['action'] == 'fetch' && isset($_GET['customer_id'])) {
    $customer_id = intval($_GET['customer_id']);
    $stmt = $conn->prepare("
        SELECT * FROM messages
        WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?)
        ORDER BY sent_at ASC
    ");
    $stmt->bind_param("iiii", $customer_id, $admin_id, $admin_id, $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // ✅ Mark all customer messages as read
    $conn->query("UPDATE messages SET is_read=1 WHERE sender_id=$customer_id AND receiver_id=$admin_id");

    echo json_encode($data);
    exit;
}

// ---------- FETCH ALL CUSTOMERS ----------
$customers_res = $conn->query("
    SELECT DISTINCT u.user_id, u.full_name 
    FROM users u
    LEFT JOIN messages m ON (m.sender_id=u.user_id OR m.receiver_id=u.user_id)
    WHERE u.role_id=3
");

if(!$customers_res) $customers_res = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>💌 Customer Messages | Admin Panel</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<style>
body { font-family: 'Poppins'; background:#f4f6f9; }
.chat-wrapper { display:flex; gap:1rem; height:70vh; }
.customers-list { width:220px; background:white; border-radius:12px; overflow-y:auto; padding:1rem; }
.customers-list div { padding:0.5rem; cursor:pointer; border-radius:8px; }
.customers-list div:hover,.customers-list div.active { background:#2563eb; color:white; }
.chat-box { flex-grow:1; background:white; border-radius:12px; display:flex; flex-direction:column; padding:1rem; }
.messages { flex-grow:1; overflow-y:auto; display:flex; flex-direction:column; gap:0.5rem; }
.msg-row { display:flex; align-items:flex-start; gap:0.5rem; }
.msg { max-width:calc(100% - 70px); padding:0.5rem 0.75rem; border-radius:12px; word-wrap:break-word; position:relative; }
.msg-customer { background:#e5e7eb; align-self:flex-start; }
.msg-admin { background:#2563eb; color:white; align-self:flex-end; }
.msg-time { font-size:10px; color:#f3f4f6; margin-top:3px; }
.msg-actions { display:flex; flex-direction:column; gap:2px; }
.msg-actions button { font-size:12px; font-weight:500; padding:2px 6px; border-radius:6px; cursor:pointer; }
</style>
</head>
<body>
<div class="flex">
    <div class="customers-list" id="customersList">
        <?php if($customers_res && $customers_res->num_rows>0): ?>
            <?php while($c=$customers_res->fetch_assoc()): ?>
                <div data-id="<?php echo $c['user_id']; ?>"><?php echo htmlspecialchars($c['full_name']); ?></div>
            <?php endwhile; ?>
        <?php else: ?>
            <div>No customers yet</div>
        <?php endif; ?>
    </div>

    <div class="chat-box">
        <div class="messages" id="chatMessages"></div>
        <div class="flex gap-2 mt-2">
            <input type="text" id="msgInput" class="flex-grow px-3 py-2 border rounded-full" placeholder="Type a message..." />
            <button id="sendBtn" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-full">Send</button>
        </div>
    </div>
</div>

<script>
let selectedCustomer = null;
const admin_id = <?php echo $admin_id; ?>;

// ---------- LOAD MESSAGES ----------
function loadMessages(){
    if(!selectedCustomer) return;
    $.get('messages_customer.php',{action:'fetch', customer_id:selectedCustomer}, function(data){
        const msgs = JSON.parse(data);
        let html = '';
        msgs.forEach(m => {
            const isAdmin = m.sender_id == admin_id;
            let actions = '';
            if(isAdmin){
                actions = `<div class="msg-actions">
                    <button class="bg-emerald-500 hover:bg-emerald-600 text-white" onclick="editMsg(${m.id},'${m.message.replace(/'/g,"\\'")}')">Edit</button>
                    <button class="bg-rose-500 hover:bg-rose-600 text-white" onclick="deleteMsg(${m.id})">Delete</button>
                </div>`;
            }
            html += `<div class="msg-row ${isAdmin?'justify-end':'justify-start'} items-start gap-2">
                        <div class="msg ${isAdmin?'msg-admin':'msg-customer'}">${m.message}<div class="msg-time">${new Date(m.sent_at).toLocaleString()}</div></div>
                        ${isAdmin ? actions : ''}
                     </div>`;
        });
        $('#chatMessages').html(html);
        $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
    });
}

// ---------- CUSTOMER SELECT ----------
$('#customersList div').click(function(){
    $('#customersList div').removeClass('active');
    $(this).addClass('active');
    selectedCustomer = $(this).data('id');
    loadMessages();
});

// ---------- SEND MESSAGE ----------
$('#sendBtn').click(function(){
    const msg = $('#msgInput').val().trim();
    if(!selectedCustomer || msg==='') return;
    $.post('messages_customer.php',{action:'send', customer_id:selectedCustomer, message:msg},function(res){
        if(res==='success'){
            $('#msgInput').val('');
            loadMessages();
        }
    });
});

// ---------- EDIT MESSAGE ----------
function editMsg(id, current){
    const newMsg = prompt("Edit message:", current);
    if(newMsg!==null && newMsg.trim()!==''){
        $.post('messages_customer.php',{action:'update', msg_id:id, message:newMsg},function(res){
            if(res==='success') loadMessages();
        });
    }
}

// ---------- DELETE MESSAGE ----------
function deleteMsg(id){
    if(confirm("Are you sure you want to delete this message?")){
        $.post('messages_customer.php',{action:'delete', msg_id:id},function(res){
            if(res==='success') loadMessages();
        });
    }
}

// ---------- AUTO REFRESH ----------
setInterval(loadMessages,2000);
</script>
</body>
</html>
