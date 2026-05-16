/*
* Check multiple checkboxes on page
* INPUT:
*   Checkbox_Clicked by user
* OUTPUT:
*   Checks checkboxes on page if:
*		checkbox name starts with "row_" and checkbox value == value of clicked checkbox
*		OR
*		checkbox name starts with "row_" and Checkbox_Clicked name == "checkall"
*		
*/
function checkValue( theBox ) {

	form = theBox.form;

	for(i=0; i<form.length; i++) {
		
		elementName  = form.elements[i].name.split("_", 2);
		elementValue = form.elements[i].value;
		
		if( elementName[0]=="row" && elementValue==theBox.value ) {
			
			// Set the element "checked" value = theBox "checked" value
			form.elements[i].checked = theBox.checked;
		}
	}
}

function checkAll( theBox ) {

	form = theBox.form;

	for(i=0; i<form.length; i++) {
		
		elementName  = form.elements[i].name.split("_", 2);
		elementValue = form.elements[i].value;
		
		if( elementName[0]=="row" ) {
			
			// Set the element "checked" value = theBox "checked" value
			form.elements[i].checked = theBox.checked;
		}
	}
}

function setFieldsAndSubmit(txtvalue,neworder) {
	document.getElementById('order_by').value=txtvalue;
	document.getElementById('order_dir').value=neworder;
	document.getElementById('form_order').submit();
}

function confirmSubmit(message)
{
var agree=confirm(message);
if (agree)
	return true ;
else
	return false ;
}

function ValidateForm(message)
{

   if(IsEmpty(document.getElementById(validate_txt_field))) 
   { 
      alert(message);
      form.id_txt_field.focus(); 
      return false; 
   }
   	
   document.getElementById(form_validate).submit();
   return true;
 
 
} 

