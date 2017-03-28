function iniDocumentShow() {
	window.onkeydown=function(e) {
    console.log(e.which);
		if (e.which==13 && e.ctrlKey) {
			$.post(document.forms.updatefrm.action, $(document.forms.updatefrm).serialize(), function(ok) {
				history.back()
			});
			return false;
		}
		if (e.which==27) {
			location='/documents';
		}
	}
}
function deleteDoc(id, deleteURL) {
  if (!confirm("Soll das Dokument #"+id+" wirklich gelöscht werden?")) return;
  $.ajax({
    method: 'DELETE',
    url: deleteURL,
    data: { '_token': csrf },
    success: function() {
      location="/documents"
    }
  })
}
