var unitWidth=111;
jQuery(document).ready(function(){
    var link=jQuery('.siteplan_link_expand');
    link.click(function(){
        var t=jQuery(this);
        var p=t.closest('.siteplan_block');
        var d=t.closest('.siteplan_wrapper');
        if (t.hasClass('expanded')) {
            var f=p.children('.siteplan_block');
            var f1=f.children('.siteplan_wrapper').find('.siteplan_link_expand.expanded');
            f1.trigger('click');
            t.removeClass('expanded');
        }else{
            t.addClass('expanded');
        }
        //t.toggleClass('expanded');
        if (t.hasClass('expanded')) {
            d.animate({width:unitWidth*p.attr('children')},200*p.attr('children'),'linear',function(){
                var c=jQuery(this).closest('.siteplan_block').children('.siteplan_block');
                c.slideDown(1000).promise().done(function(){

                });
                jQuery(this).css('width','auto');
            });
        }else{

            d.css('width',d.width());
            var c1=d.closest('.siteplan_block').children('.siteplan_block');
            c1.slideUp({duration:1000}).promise().done(
                function(d,k){d.delay(1000).animate({width:unitWidth},200*k,'linear')}(d,p.attr('children')) //delay
            );
        }
        return false;
    });
    link.each(function(){
        jQuery(this).html(jQuery(this).closest('.siteplan_block').attr('children'));
    });

    jQuery('.siteplan_type_link').click(function(){
        jQuery(this).closest('.siteplan_outer_wrap').find('.siteplan_context_menu')
            .fadeIn()
            .mouseleave(function(){jQuery(this).fadeOut()});
    })

});
