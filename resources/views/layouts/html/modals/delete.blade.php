<div id="confirm-delete" class="modal modal-danger">
	<div class="custom-popup">
		<div class="table-cell">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
						<h4 class="modal-title">Warning!</h4>
					</div>
					<div class="modal-body">
						<p>Record will be deleted! Are you sure?</p>
					</div>
					<div class="modal-footer">
						<a class="btn btn-outline pull-left" data-dismiss="modal">No</a>
						<a class="btn btn-outline btn-ok">Yes</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<form id="delete-form" method="post" action="">
	{!! csrf_field() !!}
	<input type="hidden" name="_method" value="delete"/>
</form>

<script type="text/javascript">
	$(function () {
		$('#confirm-delete').on('show.bs.modal', function(e) {
			if ($(e.relatedTarget).data('body')) {
				$('#confirm-delete').find('.modal-body').html('<p>' + $(e.relatedTarget).data('body') + '</p>');
			}
			$('#delete-form').attr('action',  $(e.relatedTarget).data('href'));
			$(this).find('.btn-ok').click(function(){ $('#delete-form').submit(); });
		});
	});
</script>