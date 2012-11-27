var selectDetailType;

//aliases
var Dom = YAHOO.util.Dom,
	Hul = top.HEURIST.util;

/**
*
*/
function isnull(obj){
	return ((obj===null) || (obj===undefined));// old js way typeof(obj)==='undefined');
}

/**
* SelectDetailType - class for pop-up window to select detail types for record structure
*
* public methods
*
* apply -
* cancel -
*
* @author Artem Osmakov <osmakov@gmail.com>
* @version 2011.0427
*/
function SelectDetailType() {

		var _className = "SelectDetailType",
			_myDataTable,
			_myDataSource,
			_arr_selection = [],
			rty_ID;  // the target record type (to filter out types that are already has been added)

			//
			// filtering UI controls
			//
		var filterTimeout = null,
			filterByName,
			filterByGroup,
			filterBySelection1,
			filterBySelection2,
			lblSelect1,
			lblSelect2;

		//for tooltip
		var currentTipId,
			_rolloverInfo;

	/**
	* Updates filter conditions for datatable
	*/
	var _updateFilter  = function () {
							// Reset timeout
							filterTimeout = null;

							var filter_name  = filterByName.value;
							var filter_group = filterByGroup.value;
							var filter_select = ((filterBySelection2.checked)?1:0);

							// Get filtered data
							_myDataSource.sendRequest(filter_group+'|'+filter_name+'|'+filter_select, {
								success : _myDataTable.onDataReturnInitializeTable,
								failure : _myDataTable.onDataReturnInitializeTable,
								scope   : _myDataTable,
								argument : { pagination: { recordOffset: 0 } } // to jump to page 1
							});


	};


	/**
	* Initialization of input form
	*
	* 1. Reads GET parameters
	* 2. create and fill table of detail types
	* 3. fill combobox(selector) with detail groups
	* 4. assign listeners for filter UI controls
	*/
	function _init() {

		//refill array
		// 1. Reads GET parameters
		if (location.search.length > 1) {
			//window.HEURIST.parameters = top.HEURIST.parseParams(location.search);
			top.HEURIST.parameters = top.HEURIST.parseParams(location.search);
			rty_ID = top.HEURIST.parameters.rty_ID;
			document.title = "Insert fields -> " + top.HEURIST.rectypes.names[rty_ID];
		}

		//////////////////// create data table
		var arr = [],
			dty_ID,
			fi = top.HEURIST.detailTypes.typedefs.fieldNamesToIndex;

		//2. create datatable and fill it values of particular group
		for (dty_ID in top.HEURIST.detailTypes.typedefs) {
			if(!isNaN(Number(dty_ID)))
			{
				var td = top.HEURIST.detailTypes.typedefs[dty_ID];
				var deftype = td.commonFields;

				var aUsage = top.HEURIST.detailTypes.rectypeUsage[dty_ID];

				if( deftype[fi.dty_ShowInLists]!="0" && (isnull(aUsage) || aUsage.indexOf(rty_ID)<0)){

					var iusage = isnull(aUsage) ? 0 : aUsage.length;
					var ptr_1 = isnull(deftype[fi.dty_FieldSetRectypeID])?'':deftype[fi.dty_FieldSetRectypeID];
					var ptr_2 = isnull(deftype[fi.dty_PtrTargetRectypeIDs])?'':deftype[fi.dty_PtrTargetRectypeIDs];
					// add order in group, name, help, type and status,
					// doc will be hidden (for pop-up)
					arr.push([  (_arr_selection.indexOf(dty_ID)>=0 ),
						deftype[fi.dty_OrderInGroup],
						deftype[fi.dty_Name],
						deftype[fi.dty_HelpText],
						deftype[fi.dty_Type],
						deftype[fi.dty_Status],
						deftype[fi.dty_ExtendedDescription],
						deftype[fi.dty_DetailTypeGroupID],
						dty_ID,iusage,ptr_1,ptr_2]);

				}
			}
		}//for

		//sort by name
		arr.sort(function(a,b){return a[2]>b[2]});


		if(isnull(_myDataTable)){

			_rolloverInfo = new HintDiv('inforollover33', 200, 170, '<div id="inforollover3"></div>');

								_myDataSource = new YAHOO.util.LocalDataSource(arr, {
									responseType : YAHOO.util.DataSource.TYPE_JSARRAY,
									responseSchema : {
										fields: ["selection", "order", "name", "help",  "type", "status", "description", "group", "info", "usage", "fieldset_rectypeid", "ptrtarget_rectypeids"]
									},
									doBeforeCallback : function (req, raw, res, cb) {
										// This is the filter function

										var data  = res.results || [],
										filtered = [],
										i,l;

										if (req) {
											//parse request
											var fvals = req.split("|");

											var sByGroup  = fvals[0];
											var sByName   = fvals[1].toLowerCase();
											var isBySelect = (fvals[2]==="0");

											for (i = 0, l = data.length; i < l; ++i)
											{
												if ((sByGroup==="all" || data[i].group===sByGroup) &&
												(data[i].name.toLowerCase().indexOf(sByName)>=0))
												{
													data[i].selection = (_arr_selection.indexOf(data[i].info)>=0);
													if(isBySelect || data[i].selection){
														filtered.push(data[i]);
													}
												}
											}
											res.results = filtered;
										}

										return res;
									}
								});

								var myColumnDefs = [
								{ key: "selection", label: "", sortable:true,
									formatter:YAHOO.widget.DataTable.formatCheckbox, className:'center' },
								{ key: "name", label: "Field type name", sortable:true },
								{ key: "order", label: "Order", hidden:true },
								{ key: "help", label: "Help", hidden:true, sortable:false},
								{ key: "type", label: "Data type", sortable:true,
									formatter: function(elLiner, oRecord, oColumn, oData) {
										var type = oRecord.getData("type");
										elLiner.innerHTML = "<div style='width:80px'>"+top.HEURIST.detailTypes.lookups[type]+"</div>";
									}
								},
								{ key: "status", label: "Status", hidden:true, sortable:false },
								{ key: "description",   hidden:true},
								{ key: "group",   hidden:true},
								{ key: "info", hidden:true, label: "Info", sortable:false, formatter: function(elLiner, oRecord, oColumn, oData){
										elLiner.innerHTML = '<img src="../../common/images/info.png" width="16" height="16" border="0" title="Info"/>';} },
								{ key: "usage", label: "Usage", sortable:true, width:25, className:'center'},
								{ key: "fieldset_rectypeid",   hidden:true},
								{ key: "ptrtarget_rectypeids", label: "Pointer targets", 
									formatter: function(elLiner, oRecord, oColumn, oData){
										
										var dttype = oRecord.getData('type');
										var value;
										if(dttype === "fieldsetmarker") {
											value = oRecord.getData('fieldset_rectypeid');
										} else {
											value = oRecord.getData('ptrtarget_rectypeids');
										}
										textTip = _getRecPointers(dttype, value, false);
										
										elLiner.innerHTML = "<div class='truncate'>"+textTip+"</div>";
						
								}}
								];

								var myConfigs = {
									sortedBy:{key:"name", dir:"asc"}
									//selectionMode: "singlecell",
									/*, paginator : new YAHOO.widget.Paginator({
										rowsPerPage: 10, // REQUIRED
										totalRecords: arr.length, // OPTIONAL
										containers: ['dt_pagination_top','dt_pagination_bottom'],
										// use a custom layout for pagination controls
										template: "&nbsp;Page: {PageLinks} Show {RowsPerPageDropdown} per page",
										// show all links
										pageLinks: YAHOO.widget.Paginator.VALUE_UNLIMITED
										// use these in the rows-per-page dropdown
										//, rowsPerPageOptions: [100]
									})*/
								};

								_myDataTable = new YAHOO.widget.DataTable('tabContainer', myColumnDefs, _myDataSource, myConfigs);

								//
								// subscribe on datatable events
								//
								_myDataTable.subscribe("checkboxClickEvent", function(oArgs) {
									//YAHOO.util.Event.stopEvent(oArgs.event);
									var elCheckbox = oArgs.target;
									_toggleSelection(elCheckbox);
								});

								//
								// hide tooltip on mouse over
								/*
								_myDataTable.on('cellMouseoutEvent', function (oArgs) {
									currentTipId = '';
									_rolloverInfo.close();
								});*/

								//
								// mouse over "help" column shows the datailed description (tooltip)
								//
								_myDataTable.on('cellMouseoverEvent', _showInfoToolTip)



								//
								// Subscribe to events for row selection highlighting
								//
								_myDataTable.subscribe("rowMouseoverEvent", _myDataTable.onEventHighlightRow);
								_myDataTable.subscribe("rowMouseoutEvent", _myDataTable.onEventUnhighlightRow);
								_myDataTable.subscribe("cellClickEvent", function(oArgs){

								//YAHOO.util.Event.stopEvent(oArgs.event);
								//var elTarget = oArgs.target;
								//var elTargetRow = _myDataTable.getTrEl(elTarget);
								var elTargetCell = oArgs.target;
								if(elTargetCell) {
									var oRecord = _myDataTable.getRecord(elTargetCell);
									//get first cell
									var cell = _myDataTable.getTdEl({record:oRecord, column:_myDataTable.getColumn("selection")});
									if(elTargetCell!==cell){
										var elCheckbox = cell.firstChild.firstChild;
										elCheckbox.checked = !elCheckbox.checked;
										_toggleSelection(elCheckbox);
									}
								}

								});//_myDataTable.onEventSelectRow);

								//3. init Group Combo Box Filter
								_initGroupComboBoxFilter();

								//4. init listeners for filter controls
								_initListeners();
								_updateFilter();

		} //isnull(_myDataTable)
		else{
					// all stuff is already inited, change livedata in datasource only
					_myDataSource.liveData = arr;

					//refresh table
					_myDataSource.sendRequest("", {
								success : _myDataTable.onDataReturnInitializeTable,
								failure : _myDataTable.onDataReturnInitializeTable,
								scope   : _myDataTable,
								argument : { pagination: { recordOffset: 0 } } // to jump to page 1
					});

					_resetFilters();
					_updateFilter();

		}
	}//end of initialization ==============================

	/**
	* Show popup div with information about field types
	*/
	function _showInfoToolTip(oArgs) {

		var forceHideTip = true,
			textTip = null,
			target = oArgs.target,
			column = _myDataTable.getColumn(target),
			record = _myDataTable.getRecord(target),
			event = oArgs.event;

		if(!isnull(column) && (column.key === 'ptrtarget_rectypeids' || column.key === 'usage')) {

				var dty_ID = record.getData('info'),
					newID = ((column.key === 'usage')?"u":"pt")+dty_ID;

				if(!Hul.isnull(dty_ID)){
					if(currentTipId !== newID) {
						currentTipId = newID;

						if(column.key==="usage"){

								//find all records that reference this type
								var aUsage = top.HEURIST.detailTypes.rectypeUsage[dty_ID];
								if(!isnull(aUsage)){
									textTip = "<p><ul>";
									var k;
									for (k=0; k<aUsage.length; k++) {   //<a href='editRecordType.html'></a>
										textTip = textTip + "<li>"+top.HEURIST.rectypes.names[aUsage[k]]+"</li>";
									}
									textTip = textTip + "</ul></p>";
								}
						}else{

								var dttype = record.getData('type');
								var value;
								if(dttype === "fieldsetmarker") {
									value = record.getData('fieldset_rectypeid');
								} else {
									value = record.getData('ptrtarget_rectypeids');
								}
								textTip = _getRecPointers(dttype, value, true);
						}


					} else {
						forceHideTip = false;
					}
				}
				if(!Hul.isempty(textTip)) {
					var xy = [event.clientX, event.clientY];
					//var xy = Hul.getMousePos(event);
					xy[0] = xy[0] - 80;

					_rolloverInfo.showInfoAt(xy,"inforollover3",textTip);
				}
				else if(forceHideTip) {
					currentTipId = '';
					_rolloverInfo.close();
				}
		}
	}

	/**
	* recreateRecTypesPreview - creates and fills selector for Record(s) pointers if datatype
	* is relmarker or resource
	*
	* @param type an datatype
	* @value - comma separated list of rectype IDs
	*
	* @returns div for popup tooltip
	*/
	function _getRecPointers(type, value, islist) {

		var txt = "";
		if(type === "relmarker" || type === "resource" || type === "fieldsetmarker")
		{
			var sep = "";
			if(value) {
					var arr = value.split(","),
					ind, dtName;
					for (ind=0; ind<arr.length; ind++) {
						dtName = top.HEURIST.rectypes.names[arr[ind]];
						txt = txt + (islist?"<li>":sep)+dtName+(islist?"</li>":"");
						sep = ",";
					} //for
			}else{
				txt = "";
			}

			if (islist && (txt.length > 0)){
				txt = "<p><ul>"+txt+"</ul></p>";
			}
		}
		return txt;
	}


	/**
	* Listener of checkbox in datatable
	* Adds or removes detail type ID from array _arr_selection
	* Updates info label
	* @param elCheckbox - reference to checkbox element that is clicked
	*/
	function _toggleSelection(elCheckbox)
	{
									var newValue = elCheckbox.checked;
									var oRecord = _myDataTable.getRecord(elCheckbox);

									var data = oRecord.getData();
									data.selection = newValue;
									/* it works
									var recordIndex = this.getRecordIndex(oRecord);
									_myDataTable.updateRow(recordIndex, data);
									*/
									if(newValue){
										_arr_selection.push(data.info);
									}else{
										var ind = _arr_selection.indexOf(data.info);
										if(ind>=0){
											_arr_selection.splice(ind,1);
										}
									}

									lblSelect1.innerHTML = "You selected <b>"+_arr_selection.length+"</b> field"+((_arr_selection.length>1)?"s":"");
									lblSelect2.innerHTML = lblSelect1.innerHTML;
	}

	/**
	* Fills the selector (combobox) with names of group
	* @see _init
	*/
	function _initGroupComboBoxFilter()
	{
							filterByGroup = Dom.get('inputFilterByGroup');
							var dtg_ID,
								index;

				for (index in top.HEURIST.detailTypes.groups) {
					if( !isNaN(Number(index)) ) {

						dtg_ID = top.HEURIST.detailTypes.groups[index].id;
						var grpName = top.HEURIST.detailTypes.groups[index].name;
						
						Hul.addoption(filterByGroup, dtg_ID, grpName);
						if(filterByGroup.length==2){
							filterByGroup.selectedIndex = 1;
							filterByGroup.value = dtg_ID;
						}
					}
				} //for

				filterByGroup.onchange = _updateFilter;
	}

	/**
	* Listener of btnClearSelection
	* Empties _arr_selection array
	*/
	function _clearSelection(){
							_arr_selection = [];
							lblSelect1.innerHTML = "";
							lblSelect2.innerHTML = "";
							_updateFilter();
							return false;
	}

	function _resetFilters(){
		filterByName.value = "";
		filterByGroup.selectedIndex = 0;
		filterBySelection1.checked = true;
	}


	/**
	* Assign event listener for filte UI controls
	* @see _init
	*/
	function _initListeners()
	{
							filterByName = Dom.get('inputFilterByName');
							filterByName.onkeyup = function (e) {
								clearTimeout(filterTimeout);
								setTimeout(_updateFilter, 600);
							};
							setTimeout(function(){filterByName.focus();}, 1000);

							filterBySelection1 = Dom.get('inputFilterBySelection1');
							filterBySelection1.onchange = _updateFilter;
							filterBySelection2 = Dom.get('inputFilterBySelection2');
							filterBySelection2.onchange = _updateFilter;

							lblSelect1 = Dom.get('lblSelect1');
							lblSelect2 = Dom.get('lblSelect2');
							var btnClear = Dom.get('btnClearSelection');
							if(btnClear) btnClear.onclick = _clearSelection;
	} //end init listener

	function _onDefineNewType(){

			var db = (top.HEURIST.parameters.db? top.HEURIST.parameters.db :
								(top.HEURIST.database.name?top.HEURIST.database.name:''));
			var url = top.HEURIST.basePath + "admin/structure/editDetailType.html?db="+db;

			popupSelect = Hul.popupURL(top, url,
			{	"close-on-blur": false,
				"no-resize": false,
			height: 700,
			width: 700,
				callback: function(context) {

					if(!Hul.isnull(context)){
						//refresh the local heurist
						top.HEURIST.detailTypes = context.detailTypes;

						var _dtyID = Number(context.result[0]);
						if(!isNaN(_dtyID)){
							_arr_selection.push(""+Math.abs(_dtyID));
						}

						//new field type to be added - refresh list
						_init();
					}
				}
			});
	}

	//
	//public members
	//
	var that = {

				/**
				 *	Apply form - close this window and returns comma separated list of selected detail types
				 */
				apply : function () {
						var res = _arr_selection.join(",");
						window.close(res);
				},

				/**
				 * Cancel form - closes this window
				 */
				cancel : function () {
					window.close(null);
				},

				onDefineNewType: function(){
					_onDefineNewType();
				},

				getClass: function () {
					return _className;
				},

				isA: function (strClass) {
					return (strClass === _className);
				}

	};

	_init();  // initialize before returning
	return that;

}