
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
                        // Handle success response
                        console.log(response);
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
                            error: function(xhr, status, error) {
                                // Handle error response
                                console.error(xhr.responseText);
                            }
                        });
                        // Optionally, you can close the modal after successful save
                        $('#extern-department').modal('hide'); // Changed modal ID
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(xhr.responseText);
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
    </script>