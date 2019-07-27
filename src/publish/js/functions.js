var mudaFoco = function( elementoAtual, proximoElemento ) {

    var max = elementoAtual.getAttribute('maxlength');
    if ( elementoAtual.value.length == max ) {

        proximoElemento.focus();
    }
};

var palcoShow = function( element, text ){
    element.palco.addClass('alert alert-danger').html('<span class="icon-alerta"></span>'+text);
};

var mySweetAlert = function (text, title, action, time) {

    if( typeof action == "undefined" ){action = 'success';}
    if( typeof title == "undefined" ) {title = 'Parabens';}
    if( typeof time == "undefined" )  {time = 1;}

    swal({
        title: title,
        text: text,
        type: action,
        timer: time*1000,
        showConfirmButton: false
    });
};

var mySweetAlertErro = function (text, time, title) {

    if( typeof text == "undefined"  || text=='' )  {text = 'Ocorreu um problema';}
    if( typeof title == "undefined" || title=='' ) {title = 'Ops!';}
    if( typeof time == "undefined"  || time=='' )  {time = 1;}

    mySweetAlert( text, title, 'error', time );
};

var mySweetConfirmAction = function ( dados, url, text, title, textButton ) {

    if( typeof title == 'undefined'      || title=='' )     { title = 'Tem certeza?'; }
    if( typeof text == 'undefined'       || text=='' )      { text = 'Você não será capaz de recuperar essa informação!'; }
    if( typeof textButton == 'undefined' || textButton=='' ){ textButton = 'Sim, pode prosseguir!'; }

    swal({
        title: title,
        text: text,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: textButton,
        cancelButtonText:'Cancelar',
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function (isConfirm) {

        if (isConfirm) {

            var scopeSwal = $('.confirm');
            var valueBtn  = scopeSwal.html();
            scopeSwal.attr('disabled', true).html('Processando...');

            $.ajax({
                type: 'POST',
                url: url,
                data: dados,
                dataType: 'json',
                success: function (resposta) {

                    if (typeof resposta == 'object' && typeof resposta.error == 'undefined') {
                        scopeSwal.html('Aguarde...');
                        swal({
                            title: "Processado com sucesso!",
                            text: "A pagina será recarregado.",
                            timer: 1000,
                            type: "success",
                            showConfirmButton: false
                        });
                        location.reload();
                    } else {
                        mySweetAlert( resposta.messages, 'Ops!', 'error' );
                    }

                    scopeSwal.attr('disabled', false).html(valueBtn);

                }
            });

        }

    });

};

var autotab = function () {

    $(".autotab").off().on('keyup', function () {
        var valor = $(this).val();
        valor = valor.replace(',','');
        valor = valor.replace('.','');
        valor = strReplaceAll(valor, '_','');

        if (valor.length == this.maxLength) {
            //dd($(this).next('input.autotab').attr('name'));
            //$(this).closest('.autotab').focus();
        }
    });
};

jQuery.fn.extend({
    scrollToMe: function () {
        var x = jQuery(this).offset().top - 50;
        jQuery('html,body').animate({scrollTop: x}, 800);
    }});

var palcoHide = function( element ){
    element.palco.removeClass('alert alert-danger').html('');
};

var encaminhar = function ( url ) {
    window.location.href = url;
};

function numerico() {
    if (/\D/g.test( this.value )) {
        this.value = this.value.replace(/\D/g, '');
    }
}

function strReplaceAll(string, Find, Replace) {
    try {
        return string.replace( new RegExp(Find, "gi"), Replace );
    } catch(ex) {
        return string;
    }
}

function dd(value){
    console.log(value);
}

var loadbtn = function(){

    $('.load').off().on('click', function (e) {
        $(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>&nbsp;Aguarde');
    });

};

//Btn Commons Redirect
$('.common-redirect').on('click', function (e) {
    var route = $(this).data('route');
    window.location.href = route;
});

$('.numeric').bind('keyup', numerico);

var regexCelular = /^([9][3-9]{1}[0-9]{3}[0-9]{4})|([7-9]{1}[0-9]{3}[0-9]{4})$/mg;
var regexContato = /^([9][3-9]{1}[0-9]{3}[0-9]{4})|([2-9]{1}[0-9]{3}[0-9]{4})$/mg;
var regexDDD     = /^(11|12|13|14|15|16|17|18|19|22|21|24|27|28|31|32|33|34|35|37|38|41|42|43|44|45|46|47|48|49|51|53|54|55|61|62|63|64|65|66|67|68|69|71|73|74|75|77|79|81|82|83|84|85|86|87|88|89|91|92|93|94|95|96|97|98|99)$/mg;
var regexCEP     = /^(0[1-9]{1}[0-9]{6})|([1-9]{1}[0-9]{7})$/mg;
var regexNome    = /^([\D]{2,} [\D]{1,})$/mg;
var regexEmail   = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
var csrfToken    = $('meta[name=csrf_token]').attr("content");

$(function() {
    $.ajaxSetup({
        headers : {
            'X-CSRF-TOKEN' : $('meta[name=csrf_token]').attr("content")
        }
    });
});


loadbtn();