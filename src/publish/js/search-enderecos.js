var appendSelect = function ( element, objectJson, id, texto ) {

    element.find('option').remove().end();

    if (typeof id == 'undefined') {id = '';}

    var clear = true;

    if( objectJson.length > 0 ) {
        element.append('<option value="">selecione</option>');
        $.each(objectJson, function (key, value) {
            var select = ( id != value.id ? '' : 'selected' );
            element.append('<option value="' + value.id + '" '+select+'>' + value.nome + '</option>');
            element.attr('disabled',false);
            clear = false;
        });
    }

    if( clear ) {
        element.attr('disabled',true);
        element.append('<option value="">escolha ' + texto + '</option>');
    }
};

var seachCidade = function( colecao, id ) {

    colecao.uf.off().on('change', function () {

        var scope    = $(this);
        var uf       = scope.val();
        var cidadeID = scope.attr('data-cidade');
        var bairroID = scope.attr('data-bairro');

        if( uf != '' ){
            colecao.loading.toggle();
            colecao.cidade.find('option').html('Buscando...');
            $.ajax({
                url      : '/api/traz-cidade/' + uf,
                dataType : 'json',
                headers: {
                    "X-CSRF-TOKEN":$('input[name="_token"]').val()
                },
                success  : function ( resposta ) {
                    appendSelect( colecao.cidade, resposta, cidadeID, 'uma UF' );
                    if( bairroID != '' ){
                        colecao.cidade.trigger('change');
                    }
                },
                complete: function () {
                    colecao.loading.toggle();
                }
            });
        } else{
            scope.attr('data-cidade','').attr('data-bairro','');
            appendSelect( colecao.cidade, [], '', 'uma UF' );
            appendSelect( colecao.bairro, [], '', 'uma cidade' );
        }

    });
};

var seachBairro = function( colecao, id ) {

    colecao.cidade.off().on('change', function () {

        var scope    = $(this);
        var cidadeID = scope.val();
        var bairroID = colecao.uf.attr('data-bairro');

        if( cidadeID != '' ){
            colecao.loading.toggle();
            colecao.bairro.find('option').html('Buscando...');
            $.ajax({
                url      : '/api/traz-bairro/' + cidadeID,
                dataType : 'json',
                headers: {
                    "X-CSRF-TOKEN":$('input[name="_token"]').val()
                },
                success  : function ( resposta ) {
                    appendSelect( colecao.bairro, resposta, bairroID, 'uma cidade' );
                },
                complete: function () {
                    colecao.loading.toggle();
                }
            });
        } else{
            appendSelect( colecao.bairro, [], '', 'uma UF' );
        }

    });
};

var seachCep = function( colecao ) {

    // Eventos de Endereco
    colecao.cep.off().on('keyup', function () {

        if (this.value.length == 8) {
            colecao.cep.trigger( "blur" );
        } else {
            colecao.rua.val('');
            colecao.uf.val('');
            appendSelect( colecao.cidade, [], 0, 'uma UF' );
            appendSelect( colecao.bairro, [], 0, 'um cidade' );
        }
    }).on('blur', function () {

        var clear = true;

        if ( colecao.cep.valid() && colecao.cep.val() != '' ) {

            if( colecao.cep.val() != colecao.form.attr( 'data-last-cep') ) {

                colecao.loading.toggle();

                $.ajax({
                    url: '/api/consulta-cep/' + colecao.cep.val(),
                    dataType: 'json',
                    headers: {
                        "X-CSRF-TOKEN": $('input[name="_token"]').val()
                    },
                    success: function (resposta) {

                        if (typeof resposta.error == 'undefined') {

                            colecao.rua.val(resposta.endereco);
                            colecao.uf.val(resposta.uf);
                            colecao.numero.focus();

                            if (resposta.uf != '') {
                                colecao.uf.attr('data-bairro', resposta.bairro_id).attr('data-cidade', resposta.cidade_id).trigger('change');
                            }
                            colecao.form.attr('data-last-cep', colecao.cep.val());
                            clear = false;

                        } else {
                            colecao.rua.val('').focus();
                            colecao.uf.val('');
                        }
                    },
                    beforeSend: function () {
                        colecao.rua.attr('placeholder', 'Aguarde...');
                        colecao.bairro.attr('placeholder', 'Aguarde...');
                    },
                    complete: function () {
                        colecao.rua.attr('placeholder', '');
                        colecao.bairro.attr('placeholder', '');
                        colecao.loading.toggle();
                    }
                });

            } else {clear = false;}
        }

        if ( clear ) {
            appendSelect( colecao.cidade, [], 0, 'uma UF' );
            appendSelect( colecao.bairro, [], 0, 'um cidade' );
        }
    });

};