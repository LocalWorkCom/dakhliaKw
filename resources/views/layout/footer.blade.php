<br> <br> <br> <br>
<footer class="my-2">
    <div class="footer ">
        <p>جميع الحقوق محفوظه </p>
    </div>
</footer>
@stack('scripts')
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        function resetModal() {
            $('#saveExternalDepartment')[0].reset();
            $('.text-danger').html('');
        }
        $("#saveExternalDepartment").on("submit", function(e) {

            e.preventDefault();

            // Serialize the form data
            var formData = $(this)
                .serialize(); // Changed to $(this)

            // Submit AJAX request
            $.ajax({
                url: $(this).attr(
                    'action'), // Changed to $(this)
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Handle success response
                        $('#from_departement')
                            .empty();
                        $.ajax({

                            url: "{{ route('external.departments') }}",
                            type: 'get',
                            success: function(
                                response
                            ) {
                                // Handle success response
                                var selectOptions =
                                    '<option value="">اختر الادارة</option>';
                                response
                                    .forEach(
                                        function(
                                            department
                                        ) {
                                            selectOptions
                                                +=
                                                '<option value="' +
                                                department
                                                .id +
                                                '">' +
                                                department
                                                .name +
                                                '</option>';
                                        }
                                    );
                                $('#from_departement')
                                    .html(
                                        selectOptions
                                    ); // Assuming you have a select element with id 'from_departement'

                            },
                            // error: function(xhr, status, error) {
                            //     // Handle error response
                            //     console.error(xhr.responseText);
                            // }
                        });
                        // Optionally, you can close the modal after successful save
                        resetModal();
                        $('#extern-department')
                            .modal(
                                'hide'
                            ); // Changed modal ID
                    } else {
                        $.each(response.message,
                            function(key,
                                value) {
                                $('#' + key +
                                    '-error'
                                ).html(
                                    value[0]
                                );
                            });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    if (xhr.status == 422) {
                        var errors = xhr
                            .responseJSON.errors;
                        $.each(errors, function(key,
                            value) {
                            $('#' + key +
                                '-error'
                            ).html(
                                value[0]
                            );
                        });
                    }
                }
            });
        });
    });

    function toggleDropdown() {
        var dropdownMenu = document.getElementById("dropdownMenu");
        if (dropdownMenu.style.display === "block") {
            dropdownMenu.style.display = "none";
        } else {
            dropdownMenu.style.display = "block";
        }
    }
    window.onclick = function(event) {
        if (!event.target.matches('.btn')) {
            var dropdowns = document.getElementsByClassName(
                "dropdown-menu");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.style.display === "block") {
                    openDropdown.style.display = "none";
                }
            }
        }
    }

    function toggleDropdown2() {
        var dropdownMenu = document.getElementById("dropdownMenu2");
        if (dropdownMenu.style.display === "block") {
            dropdownMenu.style.display = "none";
        } else {
            dropdownMenu.style.display = "block";
        }
    }
    window.onclick = function(event) {
        if (!event.target.matches('.btn2')) {
            var dropdowns = document.getElementsByClassName(
                "dropdown-menu2");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.style.display === "block") {
                    openDropdown.style.display = "none";
                }
            }
        }
    }

    function toggleDropdown3() {
        var dropdownMenu = document.getElementById("dropdownMenu3");
        if (dropdownMenu.style.display === "block") {
            dropdownMenu.style.display = "none";
        } else {
            dropdownMenu.style.display = "block";
        }
    }

    window.onclick = function(event) {
        if (!event.target.matches('.btn3')) {
            var dropdowns = document.getElementsByClassName(
                "dropdown-menu3");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.style.display === "block") {
                    openDropdown.style.display = "none";
                }
            }
        }
    }

    function toggleDropdown4() {
        var dropdownMenu = document.getElementById("dropdownMenu4");
        if (dropdownMenu.style.display === "block") {
            dropdownMenu.style.display = "none";
        } else {
            dropdownMenu.style.display = "block";
        }
    }

    window.onclick = function(event) {
        if (!event.target.matches('.btn4')) {
            var dropdowns = document.getElementsByClassName(
                "dropdown-menu4");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.style.display === "block") {
                    openDropdown.style.display = "none";
                }
            }
        }
    }

    function toggleDropdown5() {
        var dropdownMenu = document.getElementById("dropdownMenu5");
        if (dropdownMenu.style.display === "block") {
            dropdownMenu.style.display = "none";
        } else {
            dropdownMenu.style.display = "block";
        }
    }

    window.onclick = function(event) {
        if (!event.target.matches('.btn5')) {
            var dropdowns = document.getElementsByClassName(
                "dropdown-menu5");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.style.display === "block") {
                    openDropdown.style.display = "none";
                }
            }
        }
    }

    function toggleDropdown6() {
        var dropdownMenu = document.getElementById("dropdownMenu6");
        if (dropdownMenu.style.display === "block") {
            dropdownMenu.style.display = "none";
        } else {
            dropdownMenu.style.display = "block";
        }
    }

    window.onclick = function(event) {
        if (!event.target.matches('.btn6')) {
            var dropdowns = document.getElementsByClassName(
                "dropdown-menu6");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.style.display === "block") {
                    openDropdown.style.display = "none";
                }
            }
        }
    }
    //  for header collapsing

    $(document).ready(function() {
        $('.navbar-toggler').click(function() {
            $('.navbar-collapse').toggleClass('show');
        });

        // Close navbar when clicking outside the menu area
        $(document).click(function(event) {
            var clickover = $(event.target);
            var $navbar = $('.navbar-collapse');
            var _opened = $navbar.hasClass('show');
            if (_opened === true && !clickover.hasClass(
                    'side-nav')) {
                $navbar.removeClass('show');
            }
        });
    });

    // for file upload ******


    function updateFileInput() {
        var fileInput = document.getElementById('fileInput');
        var filesNum = document.getElementById('files_num').value;
        if (filesNum) {
            fileInput.disabled = false;
        } else {
            fileInput.disabled = true;
            document.getElementById('fileList').innerHTML = '';
        }
    }

    function uploadFils() {
        const files = document.getElementById('fileInput').files;
        const fileList = document.getElementById('fileList');
        const filesNum = parseInt(document.getElementById('files_num').value);
        // if (!filesNum) {
        //     alert("Please choose the number of books first.");
        //     document.getElementById('fileInput').value = '';
        //     return false;
        // }
        if (files.length === 0) {
            //alert("Please choose files.");
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه',
                text: 'من فضلك أختر الملفات المطلوبه',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__slow'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
            return false;
        }
        if (files.length > filesNum) {
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه',
                text: 'لا يمكنك أضافه اكثر من' + filesNum + ' ملف.',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__slow'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
            // alert('لا يمكنك أضافه اكثر من' + filesNum + ' ملف.');
            document.getElementById('fileInput').value = '';
            return false;
        }
        if (files.length < filesNum) {
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه',
                text: 'لا يمكن اضافه ملفات أقل من ' + filesNum +
                    ' ملف.',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__slow'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
            // alert('لا يمكن اضافه ملفات أقل من ' + filesNum + ' ملف.');
            document.getElementById('fileInput').value = '';
            return false;
        }
        fileList.innerHTML = ''; // Clear previous list
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const listItem = document.createElement('li');
            listItem.className =
                'list-group-item d-flex justify-content-between align-items-center';
            listItem.dataset.filename = file.name;
            const fileName = document.createElement('span');
            fileName.textContent = file.name;
            const deleteButton = document.createElement('button');
            deleteButton.className = 'btn btn-danger btn-sm';
            deleteButton.textContent = 'Delete';
            deleteButton.onclick = function() {
                fileList.removeChild(listItem);
                document.getElementById('fileInput').value = '';
            };
            listItem.appendChild(fileName);
            listItem.appendChild(deleteButton);
            fileList.appendChild(listItem);
        }
    }

    function toggleDropdown4(event) {
        event.stopPropagation();
        const menu4 = document.getElementById('dropdownMenu4');
        const menu5 = document.getElementById('dropdownMenu5');
        const menu6 = document.getElementById('dropdownMenu6');

        if (menu4.style.display === 'block') {
            menu4.style.display = 'none';
        } else {
            menu4.style.display = 'block';
            menu5.style.display = 'none';
            menu6.style.display = 'none';
        }
    }

    function toggleDropdown5(event) {
        event.stopPropagation();
        const menu4 = document.getElementById('dropdownMenu4');
        const menu5 = document.getElementById('dropdownMenu5');
        const menu6 = document.getElementById('dropdownMenu6');

        if (menu5.style.display === 'block') {
            menu5.style.display = 'none';
        } else {
            menu5.style.display = 'block';
            menu4.style.display = 'none';
            menu6.style.display = 'none';
        }
    }

    function toggleDropdown6(event) {
        event.stopPropagation();
        const menu4 = document.getElementById('dropdownMenu4');
        const menu5 = document.getElementById('dropdownMenu5');
        const menu6 = document.getElementById('dropdownMenu6');

        if (menu6.style.display === 'block') {
            menu6.style.display = 'none';
        } else {
            menu6.style.display = 'block';
            menu4.style.display = 'none';
            menu5.style.display = 'none';
        }
    }

    document.addEventListener('click', function(event) {
        const menu4 = document.getElementById('dropdownMenu4');
        const menu5 = document.getElementById('dropdownMenu5');
        const menu6 = document.getElementById('dropdownMenu6');

        if (!event.target.closest('.btn4') && !event.target.closest(
                '#dropdownMenu4')) {
            menu4.style.display = 'none';
        }

        if (!event.target.closest('.btn5') && !event.target.closest(
                '#dropdownMenu5')) {
            menu5.style.display = 'none';
        }

        if (!event.target.closest('.btn6') && !event.target.closest(
                '#dropdownMenu6')) {
            menu6.style.display = 'none';
        }
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- for input time  -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr(
        " #fromTime,#toTime, #start_time_edit, #end_time_edit, #start_time_show, #end_time_show, #fromTime, #toTime", {
            enableTime: true, // Enable time picker
            noCalendar: true, // Disable calendar view
            dateFormat: "h:i K", // Set format for 12-hour time with AM/PM
            time_24hr: false, // Use 12-hour format (set to true for 24-hour format)
            minuteIncrement: 1 // Set minute increment step
        });
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
{{-- <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>
 --}}{{-- <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-messaging.js"></script> --}}
{{-- <script src="https://www.gstatic.com/firebasejs/6.3.4/firebase.js"></script>
 --}}
{{-- <script type="module"
    src='https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js'></script>
<script type="module"
    src='https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging.js'>
</script> --}}

{{-- <script defer src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script> --}}
{{-- <script defer src="https://www.gstatic.com/firebasejs/9.0.0/firebase-auth.js"></script>
<script defer src="https://www.gstatic.com/firebasejs/9.0.0/firebase-database.js"></script> --}}
{{-- <script defer src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script> --}}


{{-- firebase code  --}}
<script type="module">
    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js";
    import {
        getMessaging,
        getToken
    } from "https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging.js";
    import {
        getAuth
    } from "https://www.gstatic.com/firebasejs/9.0.0/firebase-auth.js"; // Firebase Auth
    import {
        getDatabase
    } from "https://www.gstatic.com/firebasejs/9.0.0/firebase-database.js"; // Firebase Database

    $().ready(function() {

        var firebaseConfig = {
            apiKey: "AIzaSyBJE3YuOw1Jl5qDoC_sqyuiPnq3U0qcAdk",
            authDomain: "taftesh-74633.firebaseapp.com",
            projectId: "taftesh-74633",
            storageBucket: "taftesh-74633.appspot.com",
            messagingSenderId: "930391301074",
            appId: "1:930391301074:web:45a7ad03354d8d069dc60b",
            measurementId: "G-G2FVZL2SQ7"
        };
        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);
        const auth = getAuth(app);
        const database = getDatabase(app);

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register(
                    '/firebase-messaging-sw.js')
                .then(function(registration) {
                    // console.log(
                    //     'Service Worker registered with scope:',
                    //     registration.scope);
                }).catch(function(err) {
                    // console.log(
                    //     'Service Worker registration failed:',
                    //     err);
                });
        }
        var isAuth = {{ Auth::check() ? 'true' : 'false' }};
        var userFcmToken = {!! Auth::check() && Auth::user()->fcm_token ? json_encode(Auth::user()->fcm_token) : 'null' !!};

        // Function to get the FCM token
        function getFCMToken() {
            console.log(messaging);
            getToken(messaging, {
                    vapidKey: 'BKSKyV8Qf9J5A7TuxgYQdX9cXjZuru8zS3-UkgpGtzkRC0q_VeCj3ArzaJvCJywm-LkhTfNjYwbFuRxhb3Ycz8E'
                })
                .then((currentToken) => {
                    ///  console.log("Attempting to retrieve FCM Token...");
                    if (currentToken) {
                        //   console.log('FCM Token:', currentToken);
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: '{{ route('firebase.token') }}',
                            type: 'POST',
                            data: {
                                fcm_token: currentToken
                            },
                            dataType: 'JSON',
                            success: function(response) {
                                // Token stored
                                console.log('Token successfully stored.');
                            },
                            error: function(error) {
                                console.error('Error storing token:', error);
                            },
                        });
                    } else {
                        console.log('No registration token available. Request permission to generate one.');
                    }
                })
                .catch((err) => {
                    console.error('An error occurred while retrieving token.', err);
                });
        }

        // Only run the FCM logic if the user is authenticated and fcm_token is null
        if (isAuth === true && userFcmToken === null) {
            console.log("Authenticated user with no FCM token. Attempting to retrieve FCM token...");
            if (Notification.permission === 'default') {
                Notification.requestPermission().then(function(permission) {
                    if (permission === 'granted') {
                        console.log('Notification permission granted.');
                        getFCMToken();
                    } else {
                        console.log('Unable to get permission to notify.');
                    }
                });
            } else if (Notification.permission === 'granted') {
                getFCMToken();
            } else {
                console.log('Notifications are blocked.');
            }
        } else if (isAuth === true) {
            console.log("User already has an FCM token.");
        } else {
            console.log("User not authenticated.");
        }
    })
</script>
