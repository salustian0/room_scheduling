/***
 * @author Renan Salustiano <renansalustiano2020@gmail.com>
 * dialog message
 * @param id
 * @param message
 * @param args
 * @returns {boolean}
 */
function dialog(id, message, args) {
    let title = args.hasOwnProperty('title') ? args.title : 'Mensagem';
    let component_dialog = $(`
            <div class="modal" id="${id}">
            <div class="modal-content info">
                <div class="message-dialog">
                    <header>
                        <h4>${title}</h4>
                        <span class="btn-close fas fa-close"></span>
                    </header>
                    <main>
                        <p>${message}</p>
                    </main>
                    <footer>
                    </footer>
                </div>
            </div>
        </div>`);

    for(let key in args.buttons){
        let obj = args.buttons[key];

        if(!obj.hasOwnProperty('callback') || typeof obj.callback != 'function'){
            return false;
        }

        let _text = key;
        let _class = '';
        let _icon = '';

        if(obj.hasOwnProperty('text')){
            _text = obj.text;
        }

        if(obj.hasOwnProperty('class')){
            _class = obj.class
        }

        if(obj.hasOwnProperty('icon')){
            _icon = obj.icon
        }

        let btn = $('<button>', {
            class: 'sys-btn '+_class,
            text: _text
        })

        if(_icon != ''){
            let icon = $('<i>',{
                class: _icon
            })
            btn.prepend(icon)
        }

        btn.on('click', () =>{
            obj.callback();
            component_dialog.remove();
        });
        component_dialog.find('footer').append(btn)
    }

    if($('.message-dialog').length){
        $('.message-dialog').closest('.modal').remove();
    }
    $('body').prepend(component_dialog);
}