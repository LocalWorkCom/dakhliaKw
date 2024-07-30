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
            var formData = $(this).serialize(); // Changed to $(this)

            // Submit AJAX request
            $.ajax({
                url: $(this).attr('action'), // Changed to $(this)
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Handle success response
                        $('#from_departement').empty();
                        $.ajax({

                            url: "{{ route('external.departments') }}",
                            type: 'get',
                            success: function(response) {
                                // Handle success response
                                var selectOptions =
                                    '<option value="">اختر الادارة</option>';
                                response.forEach(function(department) {
                                    selectOptions += '<option value="' +
                                        department.id +
                                        '">' + department.name +
                                        '</option>';
                                });
                                $('#from_departement').html(
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
                        $('#extern-department').modal('hide'); // Changed modal ID
                    } else {
                        $.each(response.message, function(key, value) {
                            $('#' + key + '-error').html(value[0]);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    if (xhr.status == 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key + '-error').html(value[0]);
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
            var dropdowns = document.getElementsByClassName("dropdown-menu");
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
            var dropdowns = document.getElementsByClassName("dropdown-menu2");
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
            var dropdowns = document.getElementsByClassName("dropdown-menu3");
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
            var dropdowns = document.getElementsByClassName("dropdown-menu4");
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
            if (_opened === true && !clickover.hasClass('side-nav')) {
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
                text: 'لا يمكن اضافه ملفات أقل من ' + filesNum + ' ملف.',
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
            listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
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
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
