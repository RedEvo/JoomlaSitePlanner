function zzzzdoMenu(event, id){
    var menu=jQuery('#siteplan_menu_'+id);
/*    var m=$$('#siteplan_menu_'+id)[0];
    var e=new DOMEvent(event);

    var ns=(m.getStyle('visibility')=='visible')?'hidden':'visible';

    var doc = document.documentElement, body = document.body;
    var left = (doc && doc.scrollLeft || body && body.scrollLeft || 0);
    var top = (doc && doc.scrollTop  || body && body.scrollTop  || 0);
    m.setStyle('left',left+e.client.x-20);
    m.setStyle('top',top+e.client.y-20);
    m.setStyle('visibility',ns);*/
console.log(menu);
    
    menu.fadeIn().mouseout(function(){menu.fadeOut()});
}

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
        jQuery('#siteplan_menu_'+jQuery(this).attr('itemid'))
            .fadeIn()
            .mouseleave(function(){jQuery(this).fadeOut()});
    })

});
