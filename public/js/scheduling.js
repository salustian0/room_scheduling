$(function(){
    listScheduling();
})

function listScheduling(){
    let $content = $('#list-scheduling-content')
    let component = $(`<table class="sys-table">
            <thead>
               <tr>
                    <th>Sala</th>
                    <th>Data</th>
                    <th>Hora inicial</th>
                    <th>Hora final</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>`)

    $.ajax({
        url: `${SITE_URL}/api/agendamento/show`,
        method: 'GET',
        responseType: 'json'
    }).then( (response) => {
        let component_tbody = component.find('tbody');
        if(response.hasOwnProperty('data') && !$.isEmptyObject(response.data)){
            for(let key in response.data){
                let tr_data = response.data[key];

                let tr = $('<tr>', {
                    html: `
                        <td>${tr_data.room_name}</td>
                        <td>${formatDate(tr_data.date,false)}</td>
                        <td>${tr_data.start_time}</td>
                        <td>${tr_data.end_time}</td>
                        <td class="flex-td">
                            <button data-id="${tr_data.id}" class="delete sys-btn danger"><i class="fas fa-trash"></i>Excluir</button>
                        </td>          
                    `
                });
                component_tbody.append(tr);
            }
        }else{
            let tr = $('<tr>', {
                html:`<td colspan="6">Não existem registros</td>`
            });
            component_tbody.append(tr);
        }
        $content.empty().append(component);
    }).catch( (err) =>{
        let response = err.responseJSON  ?? {};
        if(response.code == 404){
            $content.empty();
        }
        let errors = response.errors ?? {}
        for(let key in errors){
            let error = errors[key]
            $('.messages').empty().append(`<div class="message error">${error}</div>`)
        }
    })
}

$(document).on('click', '.delete',(e) => {
    let $this = $(e.currentTarget)
    let id = $this.attr('data-id')

    dialog('modal_delete', `Deseja realmente excluir o registro #${id}?`, {
        title: 'Exclusão de sala',
        buttons: {
            yes : {
                class: 'success',
                text: 'Sim',
                icon: 'fas fa-circle-check',
                callback: () => {
                    $.ajax({
                        url: `${SITE_URL}/api/agendamento/delete/${id}`,
                        method: 'DELETE'
                    }).then( (result) => {
                        let message = result.message ?? "";
                        if(message != ""){
                            $('.messages').empty().append(`<div class="message success">${message}</div>`)
                        }
                        eval(listMethod)();
                    }).catch( (err) =>{
                        let response = err.responseJSON  ?? {};
                        let errors = response.errors ?? {}
                        for(let key in errors){
                            let error = errors[key]
                            $('.messages').empty().append(`<div class="message error">${error}</div>`)
                        }
                    })
                }
            },
            not: {
                text: 'Não',
                icon: 'fas fa-circle-xmark',
                callback: () =>{}
            }
        }
    })
})