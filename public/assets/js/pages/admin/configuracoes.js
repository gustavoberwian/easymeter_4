
(function($) {

	'use strict';

    var controller_url = '/TasksController/';

	// **
	// * Handler Select Metodo
	// **
    $('#method').on('change', function () {
        $('#command').val($(this).val());
    });

	// **
	// * Handler Botão Executar
	// **
    $('#run_custom_task').on('click', function () {
        controller_url = $('#controller').val();
        run_task({custom_task: $('#command').val()});
        return false;
    });

	// **
	// * Handler Check Selecionar todos
	// **
    $('#select_all').on('change', function () {
        if ($(this).prop('checked'))
            $('.task_checkbox').prop('checked', 'checked');
        else
            $('.task_checkbox').prop('checked', '');
    });

	// **
	// * Handler Botão Aplicar
	// **
    $('#execute_action').on('click', function () {
        var action = $('#action').find('option:selected').val();
        var tasks = $('.task_checkbox:checked').map(function () {
            return $(this).val();
        }).get();
        if ('Run' == action) {
            run_task({task_id: tasks});
        } else {
            $.post(controller_url + 'tasksUpdate', {task_id: tasks, action: action}, function () {
                window.location.reload();
            });
        }
        return false;
    });

	// **
	// * Handler Action Executar
	// **
    $('.run_task').on('click', function () {
        controller_url = $(this).attr('data-controller');
        run_task({task_id: $(this).attr('data-task-id')});
        return false;
    });

	// **
	// * Executa tarefa
	// **
    var run_task = function(data) {
        if (confirm('Are you sure?')) {
            $('#output_section').show();
            $('#task_output_container').text('Running...');
            $.post(controller_url + '/runTask', data, function (data) {
                $('#task_output_container').html(data);
            }).fail(function () {
                alert('Server error has occurred');
            });
        }
    }

	// **
	// * Handler Action Editar
	// **
    $('.task-edit').on('click', function(e) {
        
        e.preventDefault();

        $.magnificPopup.open( {
            items: {src: '/ajax/md_fechamento_unidade'},
            type: 'ajax',
            focus: '#id-bloco',
            modal:true,
            ajax: {
                settings: {
                    type: 'POST',
                    data: { fid: 44, uid: $(this).data('id')}
                }
            }
        });
    });

	// **
	// * Handler Fechar Modal
	// **
	$(document).on('click', '.modal-dismiss', function (e) {
		// para propagação
		e.preventDefault();
		// fecha a modal
		$.magnificPopup.close();
	});

}).apply(this, [jQuery]);