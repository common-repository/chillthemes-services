jQuery(document).ready(function($) {
    var servicesList = $('#chillthemes-services-list');
    servicesList.sortable({
        update: function( event, ui ) {
            opts = {
                async: true,
                cache: false,
                dataType: 'json',
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'services_sort',
                    order: servicesList.sortable( 'toArray' ).toString() 
                },
                success: function( response ) {
                    return;
                },
                error: function( xhr, textStatus, e ) {
                    alert( 'The order of the items could not be saved at this time, please try again.' );
                    return;
                }
            };
        $.ajax(opts);
        }
    });
});