var ajaxSetFilter = function( dados, urlAfter ){

    dados.btnFiltrar.on('click', function (e) {
        var scope    = $(this);
        var tt       = 0;
        var valueBtn = scope.html();

        scope.attr('disabled', true).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');

        //verificando se algum campo esta preenchido
        dados.elements.each(function(index){if( $(this).val() != '' ){tt++;}});

        if( tt > 0 ){

            $.ajax({
                type: 'POST',
                url: '/api/filter/set',
                data: dados.form.serialize(),
                dataType: 'json',
                success: function (resposta) {

                    if (typeof resposta == 'object' && typeof resposta.error == 'undefined') {
                        scope.html('Aguarde...');
                        location.href = urlAfter;
                    } else {
                        scope.attr('disabled', false).html(valueBtn);
                        mySweetAlert(resposta.messages,'Ops!','error');
                    }

                }
            });


        } else {
            mySweetAlert('Preencha pelo menos 1 campo','Ops!','error');
            scope.attr('disabled', false).html(valueBtn);
        }

    });

};

var ajaxResetFilter = function( dados, urlAfter ){

    dados.btnLimparFilter.on('click', function (e) {
        var scope    = $(this);
        var valueBtn = scope.html();
        scope.html('Processando...');
        $.ajax({
            type: 'POST',
            url: '/api/filter/reset',
            data: {origem: dados.origem.val()},
            dataType: 'json',
            success: function (resposta) {

                if (typeof resposta == 'object' && typeof resposta.error == 'undefined') {
                    scope.html('Aguarde...');
                    location.href = urlAfter;
                } else {
                    scope.attr('disabled', false).html(valueBtn);
                    mySweetAlert(resposta.messages,'Ops!','error');
                }

            }
        });
    });
};