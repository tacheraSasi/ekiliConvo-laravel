{{-- <script src="https://cdn.tiny.cloud/1/{{env('TINYMCE')}}/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    // Function to check for dark mode
    function isDarkMode() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    // Apply dark or light class based on preference
    document.body.classList.toggle('dark', isDarkMode());

    tinymce.init({
        selector: 'textarea',
        plugins: 'lists link image preview',
        toolbar: 'bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        setup: function (editor) {
            editor.on('init', function () {
                // Apply the class based on the color scheme
                const darkMode = isDarkMode();
                if (darkMode) {
                    editor.getContainer().classList.add('dark'); // Add dark class to the editor container
                }
            });
        }
    });
</script> --}}
