       <!-- jQuery -->
        <script src="{{ URL::asset('public/assets/libs/jquery/jquery.min.js')}}"></script>
		
		<!-- Slimscroll JS -->
        <script src="{{ URL::asset('public/assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
		
		<!-- Slimscroll JS -->
        <script src="{{ URL::asset('public/assets/libs/slimscroll/slimscroll.min.js')}}"></script>
        <script src="{{ URL::asset('public/assets/js/pages/slimscroll.init.js')}}"></script>
        <script src="{{ URL::asset('public/assets/libs/moment/moment.min.js')}}"></script>
        <script src="{{ URL::asset('public/assets/libs/jquery-validate/jquery.validate.min.js')}}"></script>
        <script src="{{ URL::asset('public/assets/libs/jquery-validate/additional-methods.js')}}"></script>
        <script src="{{ URL::asset('public/assets/libs/jquery-ui/jquery-ui.min.js')}}"></script>
		
        <!-- Select2 JS -->
        <script src="{{ URL::asset('public/assets/libs/select2/select2.min.js')}}"></script>
        <script src="{{ URL::asset('public/assets/js/pages/select2.init.js')}}"></script>
        <!-- Data Table JS -->
       <script src="{{URL::asset('public/assets/js/jquery.dataTables.min.js')}}"></script>
       <script src="{{URL::asset('public/assets/js/dataTables.bootstrap4.min.js')}}"></script>
       <script src="{{URL::asset('public/assets/js/dataTables.buttons.min.js')}}"></script>
       <script src="{{URL::asset('public/assets/js/buttons.bootstrap4.min.js')}}"></script>
       <script src="{{URL::asset('public/assets/js/jszip.min.js')}}"></script>
       <script src="{{URL::asset('public/assets/js/buttons.html5.min.js')}}"></script>
       <script src="{{ URL::asset('public/assets/js/pages/datatable.init.js')}}"></script>
       
        <!-- Datetimepicker JS -->
        <script src="{{ URL::asset('public/assets/js/bootstrap-datetimepicker.min.js')}}"></script>
        <script src="{{ URL::asset('public/assets/js/pages/datetimepicker.init.js')}}"></script>

        <!-- Sweetalert JS -->
        <script src="{{ URL::asset('public/assets/libs/sweetalert/sweetalert.min.js')}}"></script>
		@yield('script')
		<!-- Custom JS -->
		<script  src="{{ URL::asset('public/assets/js/app.min.js')}}"></script>
		@yield('script-bottom')
		 