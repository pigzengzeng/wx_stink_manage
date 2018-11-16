
<!--Footer-part-->

<div class="row-fluid">
  <div id="footer" class="span12">Copyright &copy; <?php echo date("Y")?>.GWRX All rights reserved.</div>
</div>

<!--end-Footer-part-->




<script src="/static/js/jquery.min.js"></script> 
<script src="/static/js/jquery.ui.custom.js"></script> 
<script src="/static/js/bootstrap.min.js"></script> 

<script src="/static/js/bootstrap-datepicker.js"></script> 

<script src="/static/js/jquery.uniform.js"></script> 
<script src="/static/js/select2.min.js"></script> 

<!--table插件-->
<script src="/static/js/jquery.dataTables.min.js"></script> 


<script type="text/javascript">
	$(document).ready(function(){
		if(!__hook_funs)return
		if(__hook_funs.length==0)return 
		for(var i=0;i<__hook_funs.length;i++){
			(__hook_funs[i])();
		}
	})

</script>

