# Messenger Chat System - Implementation Guide

## Overview
The Contact Us feature has been transformed into a **Messenger-style chat system** for your Library Management System. Users can now send messages to the admin and to other registered users.

---

## 📋 Files Created & Modified

### **New Files Created:**
1. **Database Setup Script**
   - `/kmkdt-Library/app/controller/messagingController.php` - API for all messaging operations
   - `/kmkdt-Library/setup-database.php` - Database table setup script

2. **Frontend Files**
   - `/kmkdt-Library/public/assets/css/messenger.css` - Messenger UI styles
   - `/kmkdt-Library/public/assets/js/messenger.js` - User-side messenger JavaScript
   - `/kmkdt-Library/public/assets/js/admin-messenger.js` - Admin-side messenger JavaScript

3. **Updated Pages**
   - `/kmkdt-Library/public/user/Contact.php` - User contact page (messenger interface)
   - `/kmkdt-Library/public/admin/Contacts.php` - Admin contact page (messenger interface)

### **Backup Files:**
- `/kmkdt-Library/public/user/Contact-old.php` - Original user contact page (backup)
- `/kmkdt-Library/public/admin/Contacts-old.php` - Original admin contact page (backup)

---

## 🗄️ Database Tables

Two new tables are created:

### **conversations Table**
```sql
CREATE TABLE conversations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,           -- Regular user ID
    admin_id INT DEFAULT NULL,      -- Admin/Other user ID
    created_at TIMESTAMP,            -- Conversation start time
    updated_at TIMESTAMP,            -- Last message time
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (admin_id) REFERENCES user(id)
);
```

### **messages Table**
```sql
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT NOT NULL,   -- Links to conversations
    sender_id INT NOT NULL,         -- Who sent the message
    message TEXT NOT NULL,          -- Message content
    status ENUM('sent', 'read'),   -- Message status
    created_at TIMESTAMP,           -- When sent
    FOREIGN KEY (conversation_id) REFERENCES conversations(id),
    FOREIGN KEY (sender_id) REFERENCES user(id)
);
```

---

## 🚀 Setup Instructions

### **Step 1: Create Database Tables**
Visit: `http://localhost/kmkdt-Library/setup-database.php`

This will create the `conversations` and `messages` tables automatically.

**Expected Output:**
```
✓ Table conversations created successfully or already exists.
✓ Table messages created successfully or already exists.
```

### **Step 2: Access User Messenger**
1. Log in as a regular user
2. Go to: `http://localhost/kmkdt-Library/user/Contact` (or click Contact Us in navigation)
3. You'll see the **Messenger Interface** with:
   - Left panel: List of conversations (Admin is pinned)
   - Right panel: Message thread with selected conversation
   - Input area: Type and send messages

### **Step 3: Access Admin Messenger**
1. Log in as an admin
2. Go to: `http://localhost/kmkdt-Library/admin/Contacts`
3. You'll see:
   - Left side: Library information (unchanged)
   - Right side: **User Conversations** with messenger interface
   - All user conversations listed with unread badges

---

## 💬 Features Implemented

### **User Side Features**
✅ **Automatic Admin Conversation**
- Every user automatically has a conversation with Admin (ID 1)
- Admin conversation appears in the list
- Can start messaging immediately

✅ **User-to-User Messaging**
- Click "**+ New Chat**" button
- Enter another user's email address
- Start conversation with that user

✅ **Conversation Management**
- See all conversations in left panel
- Last message preview
- Conversation timestamp
- Unread message count badges
- Click to open any conversation

✅ **Messaging Features**
- Send and receive messages
- Auto-refresh every 3 seconds
- Mark messages as read automatically
- Message timestamps (just now, 5m ago, etc.)
- Smooth message bubbles with green theme
- Enter to send, Shift+Enter for new line

✅ **UI/UX**
- Green (#97ee5b) and white theme matching system
- Responsive messenger layout
- Clean message bubbles (green for user, gray for others)
- Smooth animations and hover effects
- Mobile responsive

### **Admin Side Features**
✅ **View All User Conversations**
- See list of all users who have messaged
- User name and email
- Last message preview
- Unread message count
- Click to open conversation

✅ **Reply to Users**
- Full message history with each user
- Send messages back
- Read/unread tracking

✅ **Conversation Management**
- All conversations sorted by most recent
- Search conversations (if needed to implement)
- Real-time message updates

---

## 🔌 API Endpoints

All endpoints in: `/kmkdt-Library/app/controller/messagingController.php`

### **User Endpoints:**

**GET /messagingController.php?action=getConversations**
- Get all conversations for logged-in user
- Returns: Array of conversations with last message and unread count

**GET /messagingController.php?action=getMessages&conversation_id={id}**
- Get all messages in a conversation
- Marks messages as read
- Returns: Array of messages with sender info

**GET /messagingController.php?action=getAdminConversation**
- Get or create admin conversation
- Returns: conversation_id

**POST /messagingController.php**
- `action=sendMessage&conversation_id={id}&message={text}`
- Send message in conversation

**POST /messagingController.php**
- `action=startConversation&recipient_email={email}`
- Create new conversation with user by email

### **Admin Endpoints:**

**GET /messagingController.php?action=adminGetConversations**
- Get all user conversations (admin only)
- Returns: Array of all conversations

---

## 📝 How It Works

### **User Flow:**
1. User logs in
2. Visits Contact Us page
3. Banner animation displays (preserved as-is)
4. Messenger interface loads below
5. "Click Admin conversation to chat"
6. Or "Click + New Chat" to message another user
7. Type message and press Enter to send
8. Messages refresh every 3 seconds

### **Admin Flow:**
1. Admin logs in
2. Visits Contacts page
3. Library info on left (unchanged)
4. User conversations on right (messenger style)
5. Click user conversation to view messages
6. Type and send replies
7. Unread badges show new messages

---

## 🎨 Styling & Customization

### **Colors:**
- Primary Green: `#97ee5b`
- Dark Text: `#333333`
- Light Border: `#e0e0e0`
- User Message: Green background
- Other Messages: Light gray background

### **CSS File:**
`/public/assets/css/messenger.css` - All messenger styles

**Customization Examples:**

**Change primary color:**
```css
:root {
    --primary-color: #your-color-here;
}
```

**Adjust message bubble size:**
```css
.message-bubble {
    max-width: 70%;  /* Change this */
}
```

**Change refresh interval:**
In `messenger.js` and `admin-messenger.js`:
```javascript
const REFRESH_INTERVAL = 3000; // 3 seconds - change as needed
```

---

## 🐛 Troubleshooting

### **"Database connection failed"**
- Make sure XAMPP MySQL is running
- Check database name is "library"
- Verify user table exists

### **"Unauthorized" error**
- Make sure you're logged in
- Check session is active
- Clear browser cookies and login again

### **Messages not sending**
- Check messagingController.php exists in `/app/controller/`
- Verify CSS and JS files are loaded
- Check browser console for errors (F12)
- Make sure textarea has content

### **Conversations not loading**
- Refresh the page
- Check network tab in browser developer tools
- Verify messaging API is responding
- Check database tables exist

### **Admin can't see conversations**
- Verify admin role in database
- Make sure users have created conversations
- Check `adminGetConversations` API is accessible

---

## 📊 Database Queries

### **View all conversations:**
```sql
SELECT * FROM conversations;
```

### **View all messages:**
```sql
SELECT * FROM messages ORDER BY created_at DESC;
```

### **View unread messages:**
```sql
SELECT * FROM messages WHERE status = 'sent';
```

### **Mark all as read:**
```sql
UPDATE messages SET status = 'read' WHERE status = 'sent';
```

### **Get user conversations:**
```sql
SELECT * FROM conversations WHERE user_id = [USER_ID];
```

---

## ✨ Features Can Be Extended

1. **Message Attachments** - Add file upload capability
2. **Real-time WebSockets** - Use Socket.io for true real-time
3. **Typing Indicators** - Show "User is typing..."
4. **Read Receipts** - Show when message was read
5. **Message Reactions** - Add emoji reactions
6. **Search Messages** - Search within conversations
7. **Message Deletion** - Allow users to delete messages
8. **Group Chats** - Create group conversations
9. **Voice Messages** - Add voice message support
10. **End-to-End Encryption** - Secure messages

---

## 📱 Mobile Responsiveness

The messenger interface is fully responsive:
- **Desktop:** Side-by-side conversations and messages
- **Tablet:** Stacked layout
- **Mobile:** Full-width messenger with smooth scrolling

---

## 🔒 Security Notes

- All messages are stored in database
- User authentication required to access
- Messages are HTML-escaped to prevent XSS
- Session-based access control
- CSRF protection via PHP session

---

## 📞 Support

For issues or questions:
1. Check troubleshooting section above
2. Review browser console errors
3. Check database tables exist and have data
4. Verify all files are in correct locations
5. Make sure XAMPP services are running

---

## 🎉 You're All Set!

Your Library Management System now has a modern, **Messenger-style chat system** integrated seamlessly. Users can communicate with admins and each other directly within the system.

**Key Points:**
✓ Banner animation preserved  
✓ Green and white color scheme maintained  
✓ Responsive design  
✓ Real-time message updates  
✓ Admin dashboard unchanged  
✓ User-to-admin and user-to-user messaging  
✓ Clean, modern UI  

Enjoy your new messaging system! 🚀
