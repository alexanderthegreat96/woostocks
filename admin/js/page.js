$(document).ready(function() {
    var table = $('table.data-view').DataTable( {
        responsive: true,
        "autoWidth": false,
        lengthChange: false,
        pageLength: 15,
        "order": [[ 3, "desc" ]],

        dom: 'Bflrtip',
        buttons: true,
        buttons: [
            { extend: 'copy', text: 'Copiaza' },
            { extend: 'csv', text: 'Exporta CSV', title: 'data_export_<?php echo time();?>',footer: true,exportOptions: {
                    columns: ':visible'
                }},
            { extend: 'colvis', text: 'Ascunde Coloane' }
        ]


    } );

    /**
     * Reload page on click
     *
     */
    $("#reloadBtn").click(function()
    {
        location.reload();
    });

} );