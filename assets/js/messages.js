(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var messagesContainer = document.querySelector('.sofir-messages');
        if (!messagesContainer) return;

        var messagesList = messagesContainer.querySelector('.sofir-messages-list');
        var sendBtn = messagesContainer.querySelector('.button');
        var textarea = messagesContainer.querySelector('textarea');

        function loadMessages() {
            wp.apiFetch({
                path: '/sofir/v1/messages'
            }).then(function(messages) {
                messagesList.innerHTML = '';
                messages.forEach(function(message) {
                    var messageEl = document.createElement('div');
                    messageEl.className = 'sofir-message';
                    messageEl.innerHTML = '<strong>' + message.sender + '</strong>: ' + message.content;
                    messagesList.appendChild(messageEl);
                });
            }).catch(function(error) {
                console.error('Error loading messages:', error);
            });
        }

        if (sendBtn) {
            sendBtn.addEventListener('click', function() {
                var content = textarea.value.trim();
                if (!content) return;

                wp.apiFetch({
                    path: '/sofir/v1/messages',
                    method: 'POST',
                    data: { content: content }
                }).then(function() {
                    textarea.value = '';
                    loadMessages();
                }).catch(function(error) {
                    alert('Error sending message: ' + (error.message || 'Unknown error'));
                });
            });
        }

        loadMessages();
    });
})();
