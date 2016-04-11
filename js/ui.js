/**
 * Created by Ivan on 4/8/16.
 */
$(function() {

    $('.list-group-item').click(view_details);
    var item;

    function view_details(event){

        if ($(event.target).hasClass('detail_url')){

        }else{
            event.preventDefault();
        }
        if(event.currentTarget != item) {
            $('.list-group-item').removeClass('active');
            $('.list-group-item').find('.detail_box').slideUp("fast", function () {


            });
            $(event.currentTarget).find('.detail_box').slideDown("fast", function () {
                $(event.currentTarget).addClass('active');
                $(event.currentTarget).css('cursor', 'default');
            });
            item = event.currentTarget;
        }
    }

});
