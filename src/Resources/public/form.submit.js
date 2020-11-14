function submit(e) {
    e.preventDefault();
    let form = e.target;
    new Request({
        url: route,
        method: form._method,
        data: { foo1: 'bar1'},
        onComplete: function(){
            console.log('done');
        }
    ).send()
}
