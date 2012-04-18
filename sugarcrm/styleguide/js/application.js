!function ($) {
  $(function(){
    
    // make code pretty (styleguide only)
    window.prettyPrint && prettyPrint()

    // add tipsies to grid for scaffolding (styleguide only)
    if ($('#grid-system').length) {
      $('#grid-system').tooltip({
          selector: '.show-grid > div'
        , title: function () { return $(this).width() + 'px' }
      })
    }
    
    // toggle all stars
    $('.toggle-all-stars').on('click', function (e) {
    		$(this).closest('table').toggleClass('active'); 
    		return false;
    })

    // toggle all checkboxes
    $('.toggle-all').on('click', function (e) {
    		$('table').find(':checkbox').attr('checked', this.checked);      
    })
    
    // timeout the alerts
    setTimeout(function(){$('.alert').fadeOut().remove();},9000);

    // toggle star
    $('.icon-star').on('click', function (e) {
    		$(this).parent().toggleClass('active');
    		return false;  
    })

    // toggle more hide
    $('.more').toggle(
      function (e) {
    		$(this).parent().prev('.extend').removeClass('hide');
    		$(this).html('Less &nbsp;<i class="icon-chevron-up"></i>');
    		return false;  
      },
      function (e) {
      		$(this).parent().prev('.extend').addClass('hide');
      		$(this).html('More &nbsp;<i class="icon-chevron-down"></i>');
      		return false;  
    })

    // editable
    $('td .dblclick').hover( 
      function () {$(this).before('<i class="icon-pencil icon-sm"></i>');},
      function () {$('.icon-pencil').remove();}
  	)
    
    // Select widget
    $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});

    // fix sub nav on scroll
    var $win = $(window)
      , $nav = $('.subnav')
      , navTop = $('.subnav').length && $('.subnav').offset().top - 40
      , isFixed = 0

    processScroll()

    $win.on('scroll', processScroll)

    function processScroll() {
      var i, scrollTop = $win.scrollTop()
      if (scrollTop >= navTop && !isFixed) {
        isFixed = 1
        $nav.addClass('subnav-fixed')
      } else if (scrollTop <= navTop && isFixed) {
        isFixed = 0
        $nav.removeClass('subnav-fixed')
      }
    }

    // do this if greater than 768px page width
    if ( $(window).width() > 768) {		

    // tooltip demo
    $('body').tooltip({
      selector: "[rel=tooltip]"
    })
    $('table').tooltip({
			delay: { show: 500, hide: 10 },
      selector: "[rel=tooltip]"
    })
    $('.block, .thumbnail').tooltip({
      selector: "a[rel=tooltip]",
			placement: "bottom"
    })
    $('.navbar').tooltip({
      selector: "a[rel=tooltip]",
			placement: "bottom"
    })

    $("a[rel=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault()
      })
    $("a[rel=popoverTop]")
      .popover({
        placement: "top"
      })
      .click(function(e) {
        e.preventDefault()
      })
      
    }
    
    // column collapse
    $('.btn-left').toggle(
    function () {
      $(this).html('<i class="icon-chevron-right icon-sm"></i>');
      $('#colright').addClass('span4');
      $('#colcenter').removeClass('span12').addClass('span8'); 
      return false;
    },
    function () {
      $(this).html('<i class="icon-chevron-left icon-sm"></i>');
      $('#colright').removeClass('span4');
      $('#colcenter').addClass('span12').removeClass('span8'); 
      return false;
    }
    )
    
    // button state demo
    $('.loading')
      .click(function () {
        var btn = $(this)
        btn.button('loading')
        setTimeout(function () {
          btn.button('reset');
					$('.modal').modal('hide')
        }, 2000)
      })

		// tour
    $('#tour').on('click', function (e) {
			$('.pointsolight').prependTo('body');
    })
    
    // add a comment
    $('#folded').find('.reply').on('click', function (e) {
			$(this).parent().after('<form class="form-horizontal tcenter"><hr><textarea class="span4"></textarea><input type="submit" class="btn pull-right" value="Reply"></form>');
			return false;
    })
    
    
    // remove a close item
    $('#folded').find('[data-toggle=tab]').on('click', function (e) {
			$('.nav-tabs').find('li').removeClass('active');
    })
    
    $('.btngroup .btn').button()

    // datepicker
    $('[rel=datepicker]').datepicker({
      format: 'mm-dd-yyyy'
    })

    // colorpicker
    $('[rel=colorpicker]').colorpicker({
  		format: 'hex'
  	})

    // datagrid
    $('table.datatable').dataTable({
      "bPaginate": false,
      "bFilter": true,
      "bInfo": false,
      "bAutoWidth": true
    })

  	$('.block').hover( function () {
  	    $(this).find('.actions .btn').toggleClass('btn-success');
  	    $(this).find('.actions .btn.btn-success').css('color','#fff');
  	    return false;
  	})

    // editable example
    $('.dblclick, .ePriority, .eStatus').hover( 
      function () {$(this).before('<span class="span2"><i class="icon-pencil icon-sm"></i></span>');},
      function () {$('.icon-pencil').remove();}
  	)
  	
  })
  
  // toggle module search (needs tap logic for mobile)
	$('.addit').on('click', function () {
	    $(this).toggleClass('active');
	    $(this).parent().parent().parent().find('.form-addit').toggleClass('hide');
	    return false;
	})
	$('.search').on('click', function () {
	    $(this).toggleClass('active');
	    $(this).parent().parent().parent().find('.dataTables_filter').toggle();
	    $(this).parent().parent().parent().find('.form-search').toggleClass('hide');
	    return false;
	})
  $('#moduleTwitter.filtered input').quicksearch('#moduleTwitter article')
  $('#moduleLog.filtered input').quicksearch('#moduleLog article')
  $('#moduleRelated.filtered input').quicksearch('#moduleRelated article')
  $('#moduleActivity.filtered input').quicksearch('#moduleActivity article')
  $('#moduleActivity.filtered input').quicksearch('#moduleActivity .results li')
  
}(window.jQuery)