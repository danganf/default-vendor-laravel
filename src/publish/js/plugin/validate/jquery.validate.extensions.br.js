function getAge(value) {
    var today = new Date();
    var from = value.split("/");
    var birthDate = new Date(from[2], from[1] - 1, from[0]);
    //var birthDate = new Date(dateString);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

jQuery.validator.addMethod("validCep", function (value, element) {
    return ($("#validCEP").val() == "true");
}, "CEP inválido");


jQuery.validator.addMethod("over18", function (value, element) {
    return (getAge(value) >= 18);
}, "Idade mínima de 18 anos");

jQuery.validator.addMethod("cpf", function (value, element) {
    var cpf = jQuery.trim(value).replace(/[^\d]+/g, '');
    if (cpf == '') return false; // Elimina CPFs invalidos conhecidos
    if (cpf.length != 11 ||
        cpf == "00000000000" ||
        cpf == "11111111111" ||
        cpf == "22222222222" ||
        cpf == "33333333333" ||
        cpf == "44444444444" ||
        cpf == "55555555555" ||
        cpf == "66666666666" ||
        cpf == "77777777777" ||
        cpf == "88888888888" ||
        cpf == "99999999999")
        return false;
    // Valida 1o digito
    var add = 0;
    for (i = 0; i < 9; i++)
        add += parseInt(cpf.charAt(i)) * (10 - i);
    var rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
        rev = 0;
    if (rev != parseInt(cpf.charAt(9)))
        return false;
    // Valida 2o digito
    add = 0;
    for (i = 0; i < 10; i++)
        add += parseInt(cpf.charAt(i)) * (11 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
        rev = 0;
    if (rev != parseInt(cpf.charAt(10)))
        return this.optional(element) || false;
    return this.optional(element) || true;

}, "Informe um CPF válido."); // Mensagem padrão


jQuery.validator.addMethod("dateBR", function (value, element) {
    //contando chars
    if (value.length != 10) return (this.optional(element) || false);
    // verificando data
    var data = value;
    var dia = data.substr(0, 2);
    var barra1 = data.substr(2, 1);
    var mes = data.substr(3, 2);
    var barra2 = data.substr(5, 1);
    var ano = data.substr(6, 4);
    if (data.length != 10 || barra1 != "/" || barra2 != "/" || isNaN(dia) || isNaN(mes) || isNaN(ano) || dia > 31 || mes > 12) return (this.optional(element) || false);
    if ((mes == 4 || mes == 6 || mes == 9 || mes == 11) && dia == 31) return (this.optional(element) || false);
    if (mes == 2 && (dia > 29 || (dia == 29 && ano % 4 != 0))) return (this.optional(element) || false);
    if (ano < 1900) return (this.optional(element) || false);
    return (this.optional(element) || true);
}, "Informe uma data válida");

//http://stackoverflow.com/questions/280759/jquery-validate-how-to-add-a-rule-for-regular-expression-validation

$.validator.addMethod("regex", function (value, element, regexp) {
    var re = new RegExp(regexp);
    return this.optional(element) || re.test(value);
}, "Por favor, verifique o dado inserido.");


$.validator.addMethod("twowords", function (value, element, regexp) {
    var re = new RegExp(regexp);
    return this.optional(element) || /^((\b[a-zA-ZãáàâêíúõôóéçÁÉÍÓÚÔÃÇ0-9]{2,40})\s*){2,}$/mg.test(value);
}, "Minimo de duas letras.");