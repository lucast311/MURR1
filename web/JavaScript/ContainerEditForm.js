/*
    THERE IS A DUPLICATE OF THIS IN THE EDIT TWIG IN A SCRIPT TAG SINCE THESE FUNCTS ARE SPECIFIC TO THAT PAGE.

    I WASN'T SURE OF THIS WAS THE BEST PRACTICE OR NOT.
*/

var onLoad = function () {
    //document.getElementById("appbundle_container_containerSerial").disabled = true;
    $('#appbundle_container_containerSerial').prop('readonly', true);
    console.log(document.getElementById("appbundle_container_property"));
    //initialize('#appbundle_container_property');
};

var unlock = function () {
    //document.getElementById("appbundle_container_containerSerial").disabled = false;
    $('#appbundle_container_containerSerial').prop('readonly', false);
    document.getElementById("btnUnlock").disabled = true;
};

//var addModal = function () {
//        $('.removeButton').click(function () {
//            //console.log($(this));
//			var parent = $($(this).parent("form").get(0));
//			//console.log(parent);
//            showModal(parent.data('message'), parent);
//		});

//		//$(".ui.dropdown").dropdown();
//};

$(onLoad);