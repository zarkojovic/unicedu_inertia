<template>
    <!-- Your component template here -->
</template>

<script>

export default {
    mounted() {
        // Check if the chatbot script has already been loaded
        if (!window.chatbotScriptLoaded) {
            // Create a script element
            const script = document.createElement('script');
            script.src = 'https://salebot.ai/js/salebot.js';
            script.charset = 'utf-8';

            // Set a callback function to run once the script is loaded
            script.onload = () => {
                // Initialize SaleBotPro
                SaleBotPro.init({
                    onlineChatId: '1584',
                });

                // Set a flag to indicate that the script has been loaded
                window.chatbotScriptLoaded = true;
            };

            // Append the script element to the document's head
            document.head.appendChild(script);
        }
    },
};
</script>
