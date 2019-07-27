$(function () {
    $('.btn-change-status').on('click', function (e) {
        var scope  = $(this);
        var local  = scope.attr('data-local');
        var codigo = scope.attr('data-codigo');
        var action = scope.attr('data-action');

        if( typeof local != 'undefined' && typeof codigo != 'undefined' && typeof action != 'undefined' ){

            var dados = { "local": local, "codigo": codigo, "action": action };
            mySweetConfirmAction( dados, '/api/change-status', '' );

        } else {
            mySweetAlert('Falha ao processar', 'Ops!', 'error');
        }
    });

    $('.btn-delete').on('click', function (e) {
        var scope  = $(this);
        var local  = scope.attr('data-local');
        var codigo = scope.attr('data-codigo');

        if( typeof local != 'undefined' && typeof codigo != 'undefined' ){

            var dados = { "local": local, "codigo": codigo };
            mySweetConfirmAction( dados, '/api/delete-row' );

        } else {
            mySweetAlert('Falha ao processar', 'Ops!', 'error');
        }
    });
});