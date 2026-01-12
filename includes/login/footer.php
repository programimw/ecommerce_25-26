
<!-- Mainly scripts -->
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<!--Datatable-->
<script src="js/plugins/dataTables/datatables.min.js"></script>
<script src="js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="js/inspinia.js"></script>
<script src="js/plugins/pace/pace.min.js"></script>

<!-- Peity -->
<script src="js/plugins/peity/jquery.peity.min.js"></script>

<!-- Peity -->
<script src="js/demo/peity-demo.js"></script>

<script>
    $("#<?=$filenameArray[0]?>").addClass("active");
</script>
<!-- Toastr script -->
<script src="js/plugins/toastr/toastr.min.js"></script>


<!-- Ladda -->
<script src="js/plugins/ladda/spin.min.js"></script>
<script src="js/plugins/ladda/ladda.min.js"></script>
<script src="js/plugins/ladda/ladda.jquery.min.js"></script>


<script>

    // Handle dismiss button click
    $('[data-dismiss=modal]').on('click', clearModalContent);

    // Handle clicking outside the modal or using the escape key or dismiss button
    $('.modal').on('hidden.bs.modal', function () {
        clearModalContent.call(this);  // Call the clear function with the modal as the context
    });

    function clearModalContent() {
        var $modal = $(this);

        $modal
            .find("input,textarea,select")
            .val('')
            .end()
            .find("input[type=checkbox], input[type=radio]")
            .prop("checked", false)
            .end();

        // Unmount and remount the Stripe card element
        if (typeof cardElement !== 'undefined') {
            cardElement.unmount();  // Unmount the existing card element
            cardElement.mount('#card-element');  // Remount the card element to reset it
        }
    }
</script>

</body>

</html>
