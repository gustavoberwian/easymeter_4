(function () {
    "use strict";


    var shopping_id = $('.page-header').data("group");

    $.ajax({
        method: 'POST',
        url: '/shopping/get_lojas',
        data: {'shopping_id': shopping_id},
        dataType: 'json',
        success: function (json) {
            var select = $('#select-loja');

            $.each(json, function (i, item) {
                select.append('<option value="'
                    + item.id
                    + '">'
                    + item.nome
                    + '</option>');
            });
        },
    });

    $(document).on('click', '.btn-back', function () {
        window.history.back();
    });

    $(document).on('click', '.btn-incluir-user', function (e) {
        e.preventDefault();
        if ($(".form-user").valid()) {
            var formData = $(".form-user").serialize();
            $.ajax({
                method: 'POST',
                url: '/shopping/add_user',
                data: formData,
                dataType: 'json',
                success: function (json) {
                    if (json.status == "success") {
                        // notifica êxito
                        notifySuccess(json.message);
                    } else {
                        // notifica erro
                        notifyError(json.message);
                    }
                },
                error: function (xhr, status, error) {
                },
                complete: function () {
                    unsaved = false
                    window.location.href = "/shopping/configuracoes/" + $(".page-header").data("group");
                }
            });
        }
    });

    $(document).on('click', '.btn-edit-user', function (e) {
        e.preventDefault();
        if ($(".form-user").valid()) {
            var formData = $(".form-user").serialize();
            $.ajax({
                method: 'POST',
                url: '/shopping/edit_user',
                data: formData,
                dataType: 'json',
                success: function (json) {
                    if (json.status == "success") {
                        // notifica êxito
                        notifySuccess(json.message);
                    } else {
                        // notifica erro
                        notifyError(json.message);
                    }
                },
                error: function (xhr, status, error) {
                },
                complete: function () {
                    //location.reload();
                }
            });
        }
    });

    $(".form-user").validate({
        rules: {
            password_user: {
                minlength: 8,
            },
            password_confirmation_user: {
                minlength: 8,
                equalTo: "#password-user"
            }
        }
    })

    /**
     * Handler para habilitar edição de senha
     */
    $(document).on('click', '.change-password', function () {
        $(this).addClass('d-none');
        $('.cancel-change-password').removeClass('d-none');
        $('.show-password').removeClass('d-none');
        $('#password-confirmation-user').removeClass('d-none');
        $('#password-confirmation-user').removeAttr('disabled');
        $('#password-user').removeAttr('disabled');
    });

    /**
     * Handler para habilitar edição de senha
     */
    $(document).on('click', '.cancel-change-password', function () {
        $(this).addClass('d-none');
        $('.change-password').removeClass('d-none');
        $('.show-password').addClass('d-none');
        $('#password-confirmation-user').addClass('d-none');
        $('#password-confirmation-user').prop('disabled', true);
        $('#password-user').prop('disabled', true);
    });

    /**
     * Handler para visualizar senha
     */
    $(document).on('click', '.show-password', function () {
        var input = $(this).parent().find('input');
        if (input.attr('type') === 'password') {
            $(this).removeClass('fa-eye');
            $(this).addClass('fa-eye-slash');
            input.attr('type', 'text');
        } else {
            $(this).removeClass('fa-eye-slash');
            $(this).addClass('fa-eye');
            input.attr('type', 'password');
        }
    });

    $(document).on('click', '.btn-redir-edit-user', function () {
        window.location.href = "/shopping/users/" + $(".page-header").data("group") + "/edit/" + $("#user-id").val();
    })

}.apply(this, [jQuery]));