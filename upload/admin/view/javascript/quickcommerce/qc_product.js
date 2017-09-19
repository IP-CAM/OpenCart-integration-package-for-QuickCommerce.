$(document).ready(function () {
	// TODO: This is going to be common on more than one page so make something reusable
	var refresh = $('#form-product').find('.fa-refresh'),
		buttons = [];
		
	$.each(refresh, function (idx, icon) {
		buttons.push($(icon).parent('a'));
	});

	var triggerSync = function (e) {
		e.preventDefault();
		e.stopPropagation();

		var button = $(e.currentTarget),
		  icon = button.find('i'),
		  token = button.attr('data-token'),
		  id = button.attr('data-id');
		
		$.ajax({
			url: 'index.php?route=qc/product/sync&token=' + token + '&product_id=' + id,
			type: 'GET',
			beforeSend: function () {
				button.removeClass('btn-default').addClass('btn-warning');
			},
			success: function (data, status, xhr) {
				icon.removeClass('fa-refresh').addClass('fa-check');
				button.removeClass('btn-warning').addClass('btn-success');
				
				window.location.reload();
			},
			error: function (xhr, status, message) {
				icon.removeClass('fa-refresh').addClass('fa-refresh');
				button.removeClass('btn-warning').addClass('btn-danger');
			},
			complete: function (xhr, status) {
				
			}
		});
	};

	$.each(buttons, function (idx, button) {
		button.on('click', triggerSync);
	});
	
	$('#button-batch-action').on('click', function (e) {
		//confirm('This action may affect multiple records. Are you sure you want to proceed?');
		
		var action = $('#batch-action').val();
		
		switch (action) {
			case 'sync':
				if (!confirm('Are you sure you want to export data to QuickBooks? This window will reload when complete.')) return;

				e.preventDefault();
				e.stopPropagation();

				$.ajax({
					url: 'index.php?route=qc/product/sync&token=' + $('#batch-action').attr('data-token'),
					type: 'post',
					data: $('#form-product').serializeArray(),
					beforeSend: function () {
						//button.removeClass('btn-default').addClass('btn-warning');
					},
					success: function (data, status, xhr) {
						//icon.removeClass('fa-refresh').addClass('fa-check');
						//button.removeClass('btn-warning').addClass('btn-success');

						window.location.reload();
					},
					error: function (xhr, status, message) {
						//icon.removeClass('fa-refresh').addClass('fa-refresh');
						//button.removeClass('btn-warning').addClass('btn-danger');
					},
					complete: function (xhr, status) {

					}
				});
				
				break;
			
			case 'delete':
				if (!confirm('You are about to delete multiple records from QuickCommerce and in QuickBooks. Are you sure you want to proceed?')) return;
				
				break;
			case 'assign_accounts':
				$('#batch-assign-accounts-modal').modal('toggle');
				
				$.ajax({
					// TODO: This endpoint needs to be changed when I embed Slim PHP
					url: 'index.php?route=qc/account/getAccounts&token=' + $('#batch-action').attr('data-token'),
					type: 'get',
					dataType: 'json',
					success: function (data, status, xhr) {
						var html = '';
						html = '<option value=""></option>'; // Default item
						//window.location.reload();
						data = (typeof data === 'string') ? JSON.parse(data) : data;
						
						for (var key in data) {
							html += '<option value="' + data[key].feed_id  + '">' + data[key].name + '</option>'; // Use the QB account ID
						}
						
						$('#form-assign-product-account').find('select').each(function () {
							$(this).html(html);
						});
					},
					error: function (xhr, status, error) {
						
					}
				});
				
				break;
			case 'generate_seo_urls':
				$('#seo-rename-modal').modal('toggle');
				
				break;
		}
		
		/*$.ajax({
			url: 'index.php?route=qc/product/fetch&token=' + $(this).attr('data-token'),
			type: 'get',
			success: function (data, status, xhr) {
				window.location.reload();
			},
			error: function (xhr, status, error) {
				
			}
		});*/
	});
	
	$('.button-payment-address-apply').on('click', function (e) {
		$.ajax({
			url: 'index.php?route=qc/product/assignAccounts&token=' + $('#batch-action').attr('data-token'),
			type: 'post',
			data: $('#form-product, #form-assign-product-account').serializeArray(),
			success: function (data, status, xhr) {
				$('#batch-assign-accounts-modal').modal('toggle');
			},
			error: function (xhr, status, error) {
				alert(error);
			}
		});
	});
	
	$('#button-payment-address-cancel').on('click', function (e) {
		$('#batch-assign-accounts-modal').modal('toggle');
	});	
	
	$('#qc-seo-rename-selected').on('click', function (e) {
		if (!confirm('Are you sure you want generate optimized URLs for these products? Any previous values will be overwritten.')) return;

		e.preventDefault();
		e.stopPropagation();

		$.ajax({
			// TODO: Core feature? Probably, but otherwise needs to be turned into an installable extension
			url: 'index.php?route=catalog/product/generateSeoUrls&token=' + $('#batch-action').attr('data-token'),
			type: 'post',
			data: $('#form-seo-rename').serializeArray(),
			beforeSend: function () {
				//button.removeClass('btn-default').addClass('btn-warning');
			},
			success: function (data, status, xhr) {
				//icon.removeClass('fa-refresh').addClass('fa-check');
				//button.removeClass('btn-warning').addClass('btn-success');

				//window.location.reload();
			},
			error: function (xhr, status, message) {
				//icon.removeClass('fa-refresh').addClass('fa-refresh');
				//button.removeClass('btn-warning').addClass('btn-danger');
			},
			complete: function (xhr, status) {

			}
		});
	});
	
	$('#qc-qbo-import').on('click', function (e) {
		confirm('Are you sure you want to import data from QuickBooks? This window will reload when complete.');
		
		$.ajax({
			url: 'index.php?route=qc/product/fetch&token=' + $(this).attr('data-token'),
			type: 'get',
			success: function (data, status, xhr) {
				window.location.reload();
			},
			error: function (xhr, status, error) {
				
			}
		});
	});
	
	$('#qc-peer-import').on('click', function (e) {
		//confirm('Are you sure you want to import data from QuickBooks? This window will reload when complete.');
		
		$('#p2p-import-modal').modal('toggle');
		
		/*$.ajax({
			url: 'index.php?route=qc/product/fetch&token=' + $(this).attr('data-token'),
			type: 'get',
			success: function (data, status, xhr) {
				window.location.reload();
			},
			error: function (xhr, status, error) {
				
			}
		});*/
	});
	
	$('#qc-qbo-export').on('click', function (e) {
		confirm('Are you sure you want to export data to QuickBooks? This window will reload when complete.');
		
		$.ajax({
			url: 'index.php?route=qc/product/push&token=' + $(this).attr('data-token'),
			type: 'get',
			success: function (data, status, xhr) {
				window.location.reload();
			},
			error: function (xhr, status, error) {
				
			}
		});
	});
	
	// Parent
	$('input[name=\'parent\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/product/autocomplete&token=' + $(this).attr('data-token') + '&filter_name=' +  encodeURIComponent(request),
				dataType: 'json',			
				success: function(json) {
					json.unshift({
						parent_id: 0,
						name: ''
					});
					
					response($.map(json, function(item) {
						return {
							label: item['name'],
							value: item['product_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'parent\']').val(item['label']);
			$('input[name=\'parent_id\']').val(item['value']);
		}	
	});

	// Category
	$('#form-product-p2p-filter input[name=\'category\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/category/db2_autocomplete&token=' + $(this).attr('data-token') + '&filter_name=' +  encodeURIComponent(request),
				dataType: 'json',			
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['name'],
							value: item['category_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'category\']').val('');
			
			$('#form-product-p2p-filter #product-category' + item['value']).remove();
			
			$('#form-product-p2p-filter #product-category').append('<div id="product-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_category[]" value="' + item['value'] + '" /></div>');	
		}
	});

	$('#form-product-p2p-filter #product-category').delegate('.fa-minus-circle', 'click', function() {
		$(this).parent().remove();
	});
	
	// Category
	$('#form-seo-rename-product-filter input[name=\'category\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/category/autocomplete&token=' + $(this).attr('data-token') + '&filter_name=' +  encodeURIComponent(request),
				dataType: 'json',			
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['name'],
							value: item['category_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'category\']').val('');
			
			$('#form-seo-rename-product-filter #product-category' + item['value']).remove();
			
			$('#form-seo-rename-product-filter #product-category').append('<div id="product-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_category[]" value="' + item['value'] + '" /></div>');	
		}
	});

	$('#form-seo-rename-product-filter #product-category').delegate('.fa-minus-circle', 'click', function() {
		$(this).parent().remove();
	});
	
	var pageChange = function (el) {	
		var link = document.createElement('a');
		link.href = $(el).attr('href');
		
		var query = link.search;
		
		query = query.trim('?').split('&'); // Trim query separator and breat into chunks
		
		var page = 1;
		for (var idx = 0; idx < query.length; idx++) {
			if (query[idx].match(/^page=/)) {
				page = query[idx].split('=')[1];
			}
		}
		
		getImportList(page);
	};
	
	var getImportList = function (page) {
		var data = $('#form-product-p2p-filter').serialize(),
            token = $('#p2p-import-modal').attr('data-token');
		
		$.ajax({
			url: 'index.php?route=catalog/product/getImportList&token=' + token + '&' + data,
			type: 'GET',
			data: {
				page: (page) ? page : 1
			},
			success: function (html) {
				$('#form-product-p2p-import').html(html);
				// Attach paging handler to returned html
				var paging = $('#form-product-p2p-import').find('.pagination').first();
				paging.on('click', 'li a', function (e) {
					e.preventDefault();
					e.stopPropagation();
					
					pageChange($(this));
				});
			}
		});
	};
	
	
	$('#button-p2p-import-filter').on('click', function (e) {
		getImportList();
	});
	
	$('#qc-peer-import-selected').on('click', function (e) {
        var token = $('#p2p-import-modal').attr('data-token');
        
		$.ajax({
			url: 'index.php?route=qc/product_import_caffetech/merge&token=' + token,
			data: $('#form-product-p2p-import, #import-images, #import-categories, #import-attributes, #import-options').serializeArray(),
			type: 'POST',
			success: function () {
				
			},
			error: function () {
				
			},
			complete: function () {
				
			}
		});
	});
    
    $('.qc-load-attribute-template').on('click', function (e) {
        var data = $('#qc-load-attributes-form').serializeArray(),
            token = $('#qc-load-attribute-template').attr('data-token');
        
        // Get attribtues by group
		$.ajax({
			url: 'index.php?route=catalog/attribute_template/getTemplateAttributes&token=' + token,
			data: data,
			type: 'POST',
			success: function (data, status, xhr) {
                $.each(data, function (groupId, obj) {
                    //console.log(obj);
                    $.each(obj, function (idx, attribute) {
                        // OC func should be in global scope
                        var params = { 
                          id: attribute['attribute_id'], 
                          name: attribute['name'], 
                          text: attribute['value'] 
                       };
                       
                       //console.log(attribute);
                       //console.log(params);
                       
                       addAttribute(params);
                    });
                });
				
			},
			error: function () {
				
			},
			complete: function () {
				
        
			}
		});
	});
	
	var getSeoRenameResultsList = function (page) {
		var data = $('#form-seo-rename-product-filter').serialize(),
            token = $('#p2p-import-modal').attr('data-token');
		
		$.ajax({
			url: 'index.php?route=catalog/product/getSeoRenameResultsList&token=' + token + '&' + data,
			type: 'GET',
			data: {
				page: (page) ? page : 1
			},
			success: function (html) {
				$('#form-seo-rename').html(html);
				// Attach paging handler to returned html
				var paging = $('#form-seo-rename').find('.pagination').first();
				paging.on('click', 'li a', function (e) {
					e.preventDefault();
					e.stopPropagation();
					
					pageChange($(this));
				});
			}
		});
	};
	
	$('#button-seo-rename-filter').on('click', function (e) {
		getSeoRenameResultsList();
	});
	
	getSeoRenameResultsList();
    
    
    // TODO: Move this Bind stuff to its own js file! It only needs to be in the extended qc product page
    if (typeof Bind !== 'undefined') {
        // Generic logic to allow for some kind of data binding in OpenCart
        // This whole file needs to be split up later, but yeah let's roll
        
        //console.clear();
        
        var viewmodel = {},
            bindings = {};
        
        var output = null;
        var prefix = '';
        var expr = /^product_(attribute|discount|special)\[\d+\]/;
        
        var buildBindParams = function (source, target, filterExpr, prefix) {
            var viewmodel = {},
                bindings = {};
            
            var form = $(target).find('input, select, textarea'),
                filtered = form.filter(function (idx, obj) {
                    if (filterExpr != false) {
						return !obj.name.match(filterExpr);
					} else {
						return true;
					}
                });
                
            $.each(filtered, function (idx, item) {
                var id = '',
					name = '',
                    selector = '',
                    sourceSelector = '',
                    formSelector = '',
                    value = null,
					checked,
					hidden;

				if (typeof item !== 'undefined' && item.name !== '') {
                    if (item.name.match(/(\[|\])/)) {
						// Radio buttons and checkboxes
                        if (item.name.match(/(\[\])/)) {
							name = item.name.replace('[]', '');
							sourceSelector = [target, '[name^=' + name + ']'].join(' ');
							formSelector = [source, '[name^=' + name + ']'].join(' ');
							selector = [sourceSelector, formSelector].join(', ');

							if (item.type === 'checkbox') {
								// TODO: Not sure if this works for radio buttons
								checked = $(item).is(':checked');
								if (checked) {
									value = $(item).val();
								}
							} else if (item.type === 'hidden') {
								hidden = true;
								value = $(item).val();
							}

						} else {
							id = name = item.id;
							selector = [target, '#' + id].join(' ');
							formSelector = [source, '#' + id].join(' ');
							selector = [selector, formSelector].join(', ');
						}

                    } else {
                        name = item.name;
                        selector = [target, '[name=' + name + ']'].join(' ');
						formSelector = [source, '[name=' + name + ']'].join(' ');
						selector = [selector, formSelector].join(', ');
                    }

                    var el = $(selector);

                    if (el.length > 0) {
                        //el[0].tagName.toLowerCase();
						// TODO: Clean this up
						if (item.name.match(/(\[\])/)) {
							if (!(viewmodel.hasOwnProperty(name))) {
								viewmodel[name] = [];
							}

							if ((checked || hidden) && viewmodel.hasOwnProperty(name)) {
								viewmodel[name].push(value); // Pull values from form on init
							}
						} else {
							viewmodel[name] = value; // Pull values from form on init
						}

                        bindings[prefix + name] = selector;
                    }
                }
            });

			//console.log(bindings);
			//console.log(viewmodel);

            return { 
                bindings: bindings, 
                viewmodel: viewmodel 
            };
        };

		// TODO: Post issue and submit pull request for fix
		// https://github.com/remy/bind.js/issues
		// bind.js can't bind an un-indexed array of hidden input fields to a corresponding array of input fields
		// ie: we can't bind <input name='hidden_input1[]' value='1'/><input name='hidden_input1[]' value='2'/>
		// won't map to <input name='hidden_input2[] value='1'/> <input name='hidden_input2[]' value='2'/>
		// Instead, we get: <input name='hidden_input2[] value='1,2'/>
		// This issue only occurs on initial binding, and as far as I know it could only affect hidden inputs,
		// but I suspect that it affects any array of elements that is not a standard 'array' input (for instance,
		// a checkbox or radio array works fine)
        console.clear();
		var fixArrayInputValues = function (source, target, viewmodel, filterExpr) {
			var form = $(target).find('input, select, textarea'),
				filtered = form.filter(function (idx, obj) {
					if (filterExpr != false) {
						return !obj.name.match(filterExpr);
					} else {
						return true;
					}
				});

			var fix = [];

			$.each(filtered, function (idx, item) {
				var name = '',
					selector = '';

				if (typeof item !== 'undefined' && item.name !== '') {
					if (item.name.match(/(\[|\])/)) {
						if (item.name.match(/(\[\])/) &&
							['radio', 'checkbox'].indexOf(item.type) === -1) {
							name = item.name.replace('[]', '');
							selector = [target, '[name^=' + name + ']'].join(' ');
                            
                            if (fix.indexOf(selector) === -1) {
								fix.push(selector);
							}
                            
							selector = [source, '[name^=' + name + ']'].join(' ');

							if (fix.indexOf(selector) === -1) {
								fix.push(selector);
							}
						}
					}
				}
			});

			if (fix.length > 0) {
				$.each(fix, function (idx, selector) {
					var el = $(selector),
						values;

					if (el.length > 0) {
						// TODO: Move this outside, anonymous loops in loops suck
						el.each(function (inputIdx, input) {
                            var prop = input.name.replace('[]', ''),
                                value;

                            
                            if (viewmodel.hasOwnProperty(prop)) {
                                // Fix
                                console.log('prop: ' + prop);
                                console.log('fix value: ' + viewmodel[prop][inputIdx]);
                                //$(input).val(viewmodel[prop][inputIdx]); // This isn't working
                                $(input).attr('value', viewmodel[prop][inputIdx]);
                            }
						});
					}
				});
			}
		};
        
        // TODO: Above function can only take one selector at a time
        var params = buildBindParams('#form-product', '#qc-customizer-product-details', expr, 'basic.');

        viewmodel = params.viewmodel;
        bindings = params.bindings;
        delete params;
        
        var params = buildBindParams('#tab-general', '#qc-customizer-product-assignment', expr, 'basic.');
        viewmodel = $.extend(viewmodel, params.viewmodel);
        bindings = $.extend(bindings, params.bindings);
        delete params;
        
        var params = buildBindParams('#qc-customizer-product-questions', '#qc-customizer-product-questions', expr, 'questions.');
        bindings = $.extend(bindings, params.bindings);
        delete params;
        
        bindings.basic = {
			callback: function () {
				//console.log(this.__export());
				//document.querySelector('#output').innerHTML = escape(JSON.stringify(this.__export(), '', 2));
        	}
		};
        
        var dimensions = $('fieldset#qc-customizer-product-dimensions'),
            stock = $('fieldset#qc-customizer-product-stock');
        
        bindings.questions = { callback: function (fields) {
            //console.clear();

            if (parseInt(fields.track_quantity) === 1) {
                stock.show();
            } else {
                stock.hide();
            }
            
            if (parseInt(fields.shipping) === 1) {
                dimensions.show();
            } else {
                dimensions.hide();
            }
            
            if (parseInt(fields.has_dimensions) === 1) {
                dimensions.show();
            } else {
                dimensions.hide();
            }
            
            if (parseInt(fields.has_vendor) === 1) {
                console.log('enabling costs');
            }
            
            if (parseInt(fields.is_bookable) === 1) {
                //console.log('this resource can be booked');
            }
        }};

        var config = {
            questions: {
                shipping: null, // This question triggers the product shipping field, just use the regular field name
                has_vendor: null,
                has_dimensions: null,
                track_quantity: null,
                is_bookable: null
            },
            basic: viewmodel
        };
        
        var customizerBindings = Bind(config, bindings);
        
        fixArrayInputValues('#tab-general', '#qc-customizer-product-assignment', viewmodel, expr);

		var bindOptions = function () {
			console.log('trigger click');
			var expr = false;
			var params = buildBindParams('#tab-option .tab-content', '#qc-customizer-options .tab-content', expr, 'options.');
			viewmodel = params.viewmodel;
			bindings = params.bindings;

			var config = {
				options: viewmodel
			};

			bindings.options = {
				callback: function () {
					//console.log(this.__export());
					//document.querySelector('#output').innerHTML = escape(JSON.stringify(this.__export(), '', 2));
				}
			};

			var b = Bind(config, bindings);
		};

		// Bind option values
		$('#tab-option, #qc-customizer-options').on('click', 'tfoot button', function (e) {
			bindOptions();
		});

		bindOptions();

		var bindPricing = function () {
			console.log('trigger click');
			var expr = false;
			var params = buildBindParams('#tab-pricing', '#qc-customizer-product-pricing', expr, 'pricing.');
			viewmodel = params.viewmodel;
			bindings = params.bindings;

			var config = {
				pricing: viewmodel
			};

			bindings.pricing = {
				callback: function () {
					console.log(this.__export());
					//document.querySelector('#output').innerHTML = escape(JSON.stringify(this.__export(), '', 2));
				}
			};

			console.log(config);
			console.log(bindings);

			var b = Bind(config, bindings);
		};

		$('#tab-pricing, #qc-customizer-product-pricing').on('click', 'tfoot button', function (e) {
			bindPricing();
		});

		bindPricing();

		var bindAttributes = function () {
			console.log('trigger click');
			var expr = false;
			var params = buildBindParams('#tab-attribute', '#qc-customizer-attributes', expr, 'attributes.');
			viewmodel = params.viewmodel;
			bindings = params.bindings;

			var config = {
				attributes: viewmodel
			};

			bindings.attributes = {
				callback: function () {
					//console.log(this.__export());
					//document.querySelector('#output').innerHTML = escape(JSON.stringify(this.__export(), '', 2));
				}
			};

			console.log(config);
			console.log(bindings);

			var b = Bind(config, bindings);
		};


		$('#tab-attribute, #qc-customizer-attributes').on('click', 'tfoot button', function (e) {
			bindAttributes();
		});

		bindAttributes();

		//console.clear();
        
        $('#qc-customizer-product-type').on('change', function (e) {
            var type = $(e.target).val();
            
            switch (type) {
                case 'inventory':
                console.log('inventory');
                customizerBindings.questions.has_vendor = 1;
                customizerBindings.questions.track_quantity = 1;
                customizerBindings.questions.shipping = 1;
                customizerBindings.questions.has_dimensions = 1;
                customizerBindings.questions.is_bookable = 0;
                break;
                
                case 'noninventory':
                console.log('noninventory');
                customizerBindings.questions.has_vendor = 1;
                customizerBindings.questions.track_quantity = 0;
                customizerBindings.questions.shipping = 0;
                customizerBindings.questions.has_dimensions = 1;
                customizerBindings.questions.is_bookable = 0;
                break;
                
                case 'downloadable':
                console.log('downloadable');
                customizerBindings.questions.has_vendor = 0;
                customizerBindings.questions.track_quantity = 0;
                customizerBindings.questions.shipping = 0;
                customizerBindings.questions.has_dimensions = 0;
                customizerBindings.questions.is_bookable = 0;
                break;
                
                case 'service':
                console.log('service');
                customizerBindings.questions.has_vendor = 0;
                customizerBindings.questions.track_quantity = 0;
                customizerBindings.questions.shipping = 0;
                customizerBindings.questions.has_dimensions = 0;
                customizerBindings.questions.is_bookable = 1;
                break;
                
                case 'resource':
                console.log('resource');
                customizerBindings.questions.has_vendor = 0;
                customizerBindings.questions.track_quantity = 1;
                customizerBindings.questions.shipping = 0;
                customizerBindings.questions.has_dimensions = 0;
                customizerBindings.questions.is_bookable = 1;
                break;
            }
            
            console.log(customizerBindings);
        });
        
        // helper to dump the object in a <pre>
        /*function escape(s) {
          return (s||'').replace(/[<>]/g, function (m) {
            return {
              '<': '&lt;',
              '>': '&gt;',
            }[m]
          })
        }*/
    }
    
    var token = $('#p2p-import-modal').attr('data-token');
    
    var indicators = $('#form-product').find('span[data-id]'),
        syncIds = [];
        
    indicators.each(function (idx, el) {
        syncIds.push($(el).attr('data-id')); 
    });
    
    $.ajax({
        type: 'post',
        url: 'index.php?route=qc/product/getSyncStatuses&token=' + token,
        data: {
            selected: syncIds 
        },
        success: function (data, status, xhr) {
            $.each(data, function (id, status) {
                var indicator = $('#form-product').find('span.label[data-id=' + id + ']'),
                    icon = indicator.find('i.fa');
                
                switch (status) {
                    case 'localonly':
                        indicator.attr('class', 'label label-info');
                        icon.attr('class', 'fa fa-thumb-tack');
                        break;
                        
                    case 'unlinked':
                        indicator.attr('class', 'label label-danger');
                        icon.attr('class', 'fa fa-unlink');
                        break;
                        
                    case 'remotenewer':
                        indicator.attr('class', 'label label-warning');
                        icon.attr('class', 'fa fa-download');
                        break;
                        
                    case 'localnewer':
                        indicator.attr('class', 'label label-success');
                        icon.attr('class', 'fa fa-upload');
                        break;
                        
                    case 'ok':
                        indicator.attr('class', 'label label-default');
                        icon.attr('class', 'fa fa-check');
                        break;
                }
            });
        },
        error: function (message) {
            console.log(message);
        }
    });
    
    $('#button-close-load-attributes').on('click', function (e) {
		$('#qc-load-attributes-modal').modal('toggle');
	});	
	
	$('.qc-load-attribute-template').on('click', function (e) {
		e.preventDefault();
        e.stopPropagation();
        
		$('#qc-load-attributes-modal').modal('toggle');
	});
    
    $('#qc-product-customizer').on('click', function (e) {
		e.preventDefault();
        e.stopPropagation();
        
		$('#qc-customizer-modal').modal('toggle');
	});
    
    $('#button-close-qc-customizer').on('click', function (e) {
		$('#qc-customizer-modal').modal('toggle');
	});
});