		@if(Route::is(['applied-jobs','offered-jobs'])) 
		<!-- Search Filter -->
					<div class="row filter-row">
						<div class="col-sm-6 col-md-3">  
							<div class="form-group form-focus select-focus">
								<select class="select floating"> 
									<option>Select</option>
									<option>Development</option>
									<option>Designing</option>
									<option>Android</option>
								</select>
								<label class="focus-label">Department</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">  
							<div class="form-group form-focus select-focus">
								<select class="select floating"> 
									<option>Select</option>
									<option>Full Time</option>
									<option>Part Time</option>
									<option>Internship</option>
								</select>
								<label class="focus-label">Job Type</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3"> 
							<div class="form-group form-focus select-focus">
								<select class="select floating"> 
									<option>Select Designation</option>
									<option>Web Developer</option>
									<option>Web Designer</option>
									<option>Android Developer</option>
									<option>Ios Developer</option>
								</select>
								<label class="focus-label">Designation</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">  
							<div class="d-grid">
								<a href="#" class="btn btn-success"> Search </a>  
							</div>
						</div>
                    </div>
					<!-- Search Filter -->
				@endif
				@if(Route::is(['assets1'])) 
					<!-- Search Filter -->
					<div class="row filter-row">
						<div class="col-sm-6 col-md-3">  
							<div class="form-group form-focus">
								<input type="text" class="form-control floating">
								<label class="focus-label">Employee Name</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3"> 
							<div class="form-group form-focus select-focus">
								<select class="select floating"> 
									<option value=""> -- Select -- </option>
									<option value="0"> Pending </option>
									<option value="1"> Approved </option>
									<option value="2"> Returned </option>
								</select>
								<label class="focus-label">Status</label>
							</div>
						</div>
						<div class="col-sm-12 col-md-4">  
						   <div class="row">  
							   <div class="col-md-6 col-sm-6">  
									<div class="form-group form-focus">
										<div class="cal-icon">
											<input class="form-control floating datetimepicker" type="text">
										</div>
										<label class="focus-label">From</label>
									</div>
								</div>
							   <div class="col-md-6 col-sm-6">  
									<div class="form-group form-focus">
										<div class="cal-icon">
											<input class="form-control floating datetimepicker" type="text">
										</div>
										<label class="focus-label">To</label>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-md-2">
							<div class="d-grid">
								<a href="#" class="btn btn-success"> Search </a>  
							</div>  
						</div>     
                    </div>
					<!-- /Search Filter -->
					@endif
					@if(Route::is(['attendance-reports'])) 
					   <!-- Search Filter -->
                    <div class="row filter-row">
                        
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <input type="text" class="form-control floating">
                                <label class="focus-label">Employee Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <select class="form-control floating select">
                                        <option>
                                            Jan
                                        </option>
                                        <option>
                                            Feb
                                        </option>
                                        <option>
                                            Mar
                                        </option>
                                    </select>
                                </div>
                                <label class="focus-label">Month</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <select class="form-control floating select">
                                        <option>
                                            2020
                                        </option>
                                        <option>
                                            2019
                                        </option>
                                        <option>
                                            2018
                                        </option>
                                    </select>
                                </div>
                                <label class="focus-label">Year</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="d-grid">
                                <a href="#" class="btn btn-success"> Search </a>  
                            </div>
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                   @endif
                  @if(Route::is(['attendance'])) 
                  <!-- Search Filter -->
					<div class="row filter-row">
						<div class="col-sm-6 col-md-3">  
							<div class="form-group form-focus">
								<input type="text" class="form-control floating">
								<label class="focus-label">Employee Name</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3"> 
							<div class="form-group form-focus select-focus">
								<select class="select floating"> 
									<option>-</option>
									<option>Jan</option>
									<option>Feb</option>
									<option>Mar</option>
									<option>Apr</option>
									<option>May</option>
									<option>Jun</option>
									<option>Jul</option>
									<option>Aug</option>
									<option>Sep</option>
									<option>Oct</option>
									<option>Nov</option>
									<option>Dec</option>
								</select>
								<label class="focus-label">Select Month</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3"> 
							<div class="form-group form-focus select-focus">
								<select class="select floating"> 
									<option>-</option>
									<option>2019</option>
									<option>2018</option>
									<option>2017</option>
									<option>2016</option>
									<option>2015</option>
								</select>
								<label class="focus-label">Select Year</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">  
							<div class="d-grid">
								<a href="#" class="btn btn-success"> Search </a>  
							</div>
						</div>     
                    </div>
					<!-- /Search Filter -->
				@endif
				 @if(Route::is(['clients-list','clients']))
				<!-- Search Filter -->
					<div class="row filter-row">
						<div class="col-sm-6 col-md-3">  
							<div class="form-group form-focus">
								<input type="text" class="form-control floating">
								<label class="focus-label">Client ID</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">  
							<div class="form-group form-focus">
								<input type="text" class="form-control floating">
								<label class="focus-label">Client Name</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3"> 
							<div class="form-group form-focus select-focus">
								<select class="select floating"> 
									<option>Select Company</option>
									<option>Global Technologies</option>
									<option>Delta Infotech</option>
								</select>
								<label class="focus-label">Company</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">  
							<div class="d-grid">
								<a href="#" class="btn btn-success"> Search </a>  
							</div>
						</div>     
                    </div>
					<!-- Search Filter -->
				@endif
					 @if(Route::is(['daily-reports','employee-reports','leave-reports',]))
				     <!-- Search Filter -->
                    <div class="row filter-row mb-4">
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <input class="form-control floating" type="text">
                                <label class="focus-label">Employee</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3"> 
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>Select Department</option>
                                    <option>Designing</option>
                                    <option>Development</option>
                                    <option>Finance</option>
                                    <option>Hr & Finance</option>
                                </select>
                                <label class="focus-label">Department</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">From</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">To</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="d-grid">
                                <a href="#" class="btn btn-success"> Search </a>  
                            </div>
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                    @endif

                   @if(Route::is(['employees-list','employees'])) 
               <!-- Search Filter -->
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <input type="text" class="form-control floating">
                                <label class="focus-label">Employee ID</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <input type="text" class="form-control floating">
                                <label class="focus-label">Employee Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3"> 
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>Select Designation</option>
                                    <option>Web Developer</option>
                                    <option>Web Designer</option>
                                    <option>Android Developer</option>
                                    <option>Ios Developer</option>
                                </select>
                                <label class="focus-label">Designation</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                   @endif
                   @if(Route::is(['estimates'])) 
                    <!-- Search Filter -->
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">From</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">To</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3"> 
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>Select Status</option>
                                    <option>Accepted</option>
                                    <option>Declined</option>
                                    <option>Expired</option>
                                </select>
                                <label class="focus-label">Status</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                    @endif

                    @if(Route::is(['expense-reports']))
                     <!-- Search Filter -->
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3"> 
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>Select buyer</option>
                                    <option>Loren Gatlin</option>
                                    <option>Tarah Shropshire</option>
                                </select>
                                <label class="focus-label">Purchased By</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">From</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">To</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                    @endif

                     @if(Route::is(['expenses']))
                     <!-- Search Filter -->
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <div class="form-group form-focus">
                                <input type="text" class="form-control floating">
                                <label class="focus-label">Item Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option> -- Select -- </option>
                                    <option>Loren Gatlin</option>
                                    <option>Tarah Shropshire</option>
                                </select>
                                <label class="focus-label">Purchased By</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12"> 
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option> -- Select -- </option>
                                    <option> Cash </option>
                                    <option> Cheque </option>
                                </select>
                                <label class="focus-label">Paid By</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">From</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">To</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                   @endif

                    @if(Route::is(['invoice-reports']))
                   <!-- Search Filter -->
					<div class="row filter-row">
						<div class="col-sm-6 col-md-3"> 
							<div class="form-group form-focus select-focus">
								<select class="select floating"> 
									<option>Select Client</option>
									<option>Global Technologies</option>
									<option>Delta Infotech</option>
								</select>
								<label class="focus-label">Client</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">  
							<div class="form-group form-focus">
								<div class="cal-icon">
									<input class="form-control floating datetimepicker" type="text">
								</div>
								<label class="focus-label">From</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">  
							<div class="form-group form-focus">
								<div class="cal-icon">
									<input class="form-control floating datetimepicker" type="text">
								</div>
								<label class="focus-label">To</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">  
							<a href="#" class="btn btn-success w-100"> Search </a>  
						</div>     
                    </div>
					<!-- /Search Filter -->
					@endif

					 @if(Route::is(['invoices'])) 
                    <!-- Search Filter -->
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">From</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">To</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3"> 
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>Select Status</option>
                                    <option>Pending</option>
                                    <option>Paid</option>
                                    <option>Partially Paid</option>
                                </select>
                                <label class="focus-label">Status</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                    @endif
                    @if(Route::is(['leaves'])) 
                    <!-- Search Filter -->
                    <div class="row filter-row">
                       <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <div class="form-group form-focus">
                                <input type="text" class="form-control floating">
                                <label class="focus-label">Employee Name</label>
                            </div>
                       </div>
                       <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option> -- Select -- </option>
                                    <option>Casual Leave</option>
                                    <option>Medical Leave</option>
                                    <option>Loss of Pay</option>
                                </select>
                                <label class="focus-label">Leave Type</label>
                            </div>
                       </div>
                       <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12"> 
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option> -- Select -- </option>
                                    <option> Pending </option>
                                    <option> Approved </option>
                                    <option> Rejected </option>
                                </select>
                                <label class="focus-label">Leave Status</label>
                            </div>
                       </div>
                       <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">From</label>
                            </div>
                        </div>
                       <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">To</label>
                            </div>
                        </div>
                       <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                       </div>     
                    </div>
                    <!-- /Search Filter -->
                    @endif
                     @if(Route::is(['payments-reports'])) 
                      <!-- Search Filter -->
                    <div class="row filter-row">
                        
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">From</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">To</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                    @endif

                     @if(Route::is(['payslip-reports'])) 
                    <!-- Content Starts -->
                        <!-- Search Filter -->
                    <div class="row filter-row">
                        
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <input type="text" class="form-control floating">
                                <label class="focus-label">Employee Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <select class="form-control floating select">
                                        <option>
                                            Jan
                                        </option>
                                        <option>
                                            Feb
                                        </option>
                                        <option>
                                            Mar
                                        </option>
                                    </select>
                                </div>
                                <label class="focus-label">Month</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <select class="form-control floating select">
                                        <option>
                                            2018
                                        </option>
                                        <option>
                                            2019
                                        </option>
                                        <option>
                                            2020
                                        </option>
                                    </select>
                                </div>
                                <label class="focus-label">Year</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                    @endif
                     @if(Route::is(['project-list'])) 
                    <!-- Search Filter -->
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <input type="text" class="form-control floating">
                                <label class="focus-label">Project Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <input type="text" class="form-control floating">
                                <label class="focus-label">Employee Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3"> 
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>Select Roll</option>
                                    <option>Web Developer</option>
                                    <option>Web Designer</option>
                                    <option>Android Developer</option>
                                    <option>Ios Developer</option>
                                </select>
                                <label class="focus-label">Role</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                    @endif

                     @if(Route::is(['project-reports'])) 
                     <!-- Content Starts -->
                        <!-- Search Filter -->
                    <div class="row filter-row">
                        
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <select class="form-control floating select">
                                        <option>
                                            Name1
                                        </option>
                                        <option>
                                            Name2
                                        </option>
                                    </select>
                                </div>
                                <label class="focus-label">Project Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <select class="form-control floating select">
                                        <option>
                                            Active
                                        </option>
                                        <option>
                                            Pending
                                        </option>
                                    </select>
                                </div>
                                <label class="focus-label">Status</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                    @endif
                    @if(Route::is(['projects'])) 
                     <!-- Search Filter -->
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <input type="text" class="form-control floating">
                                <label class="focus-label">Project Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <input type="text" class="form-control floating">
                                <label class="focus-label">Employee Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3"> 
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>Select Roll</option>
                                    <option>Web Developer</option>
                                    <option>Web Designer</option>
                                    <option>Android Developer</option>
                                    <option>Ios Developer</option>
                                </select>
                                <label class="focus-label">Designation</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- Search Filter -->
                    @endif

                     @if(Route::is(['salary'])) 
                    <!-- Search Filter -->
                    <div class="row filter-row">
                       <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <div class="form-group form-focus">
                                <input type="text" class="form-control floating">
                                <label class="focus-label">Employee Name</label>
                            </div>
                       </div>
                       <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option value=""> -- Select -- </option>
                                    <option value="">Employee</option>
                                    <option value="1">Manager</option>
                                </select>
                                <label class="focus-label">Role</label>
                            </div>
                       </div>
                       <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12"> 
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option> -- Select -- </option>
                                    <option> Pending </option>
                                    <option> Approved </option>
                                    <option> Rejected </option>
                                </select>
                                <label class="focus-label">Leave Status</label>
                            </div>
                       </div>
                       <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">From</label>
                            </div>
                        </div>
                       <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">To</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                    @endif
                    @if(Route::is(['saved-jobs','users','visited-jobs','archived-jobs'])) 
                     <!-- Search Filter -->
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>Select</option>
                                    <option>Development</option>
                                    <option>Designing</option>
                                    <option>Android</option>
                                </select>
                                <label class="focus-label">Department</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>Select</option>
                                    <option>Full Time</option>
                                    <option>Part Time</option>
                                    <option>Internship</option>
                                </select>
                                <label class="focus-label">Job Type</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3"> 
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>Select Designation</option>
                                    <option>Web Developer</option>
                                    <option>Web Designer</option>
                                    <option>Android Developer</option>
                                    <option>Ios Developer</option>
                                </select>
                                <label class="focus-label">Designation</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>
                    </div>
                    <!-- Search Filter -->
                    @endif


                     @if(Route::is(['shift-scheduling'])) 
                    <!-- Search Filter -->
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <input type="text" class="form-control floating">
                                <label class="focus-label">Employee</label>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 col-md-3"> 
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>All Department</option>
                                    <option value="1">Finance</option>
                                    <option value="2">Finance and Management</option>
                                    <option value="3">Hr & Finance</option>
                                    <option value="4">ITech</option>
                                </select>
                                <label class="focus-label">Department</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-2">  
                            <div class="form-group form-focus focused">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">From</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-2">  
                            <div class="form-group form-focus focused">
                                <div class="cal-icon">
                                    <input class="form-control floating datetimepicker" type="text">
                                </div>
                                <label class="focus-label">To</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-2">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- Search Filter -->
                    @endif


                      @if(Route::is(['task-reports']))   
                        <!-- Search Filter -->
                    <div class="row filter-row">
                        
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <select class="form-control floating select">
                                        <option>
                                            Name1
                                        </option>
                                        <option>
                                            Name2
                                        </option>
                                    </select>
                                </div>
                                <label class="focus-label">Project Name</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <select class="form-control floating select">
                                        <option>
                                            All
                                        </option>
                                        <option>
                                            Pending
                                        </option>
                                        <option>
                                            Completed
                                        </option>
                                    </select>
                                </div>
                                <label class="focus-label">Status</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                    @endif
                        @if(Route::is(['user-all-jobs']))
                     <!-- Search Filter -->
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>Select</option>
                                    <option>Development</option>
                                    <option>Designing</option>
                                    <option>Android</option>
                                </select>
                                <label class="focus-label">Department</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>Select</option>
                                    <option>Full Time</option>
                                    <option>Part Time</option>
                                    <option>Internship</option>
                                </select>
                                <label class="focus-label">Job Type</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3"> 
                            <div class="form-group form-focus select-focus">
                                <select class="select floating"> 
                                    <option>Select Designation</option>
                                    <option>Web Developer</option>
                                    <option>Web Designer</option>
                                    <option>Android Developer</option>
                                    <option>Ios Developer</option>
                                </select>
                                <label class="focus-label">Designation</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>
                    </div>
                    <!-- Search Filter -->
                    @endif
                     @if(Route::is(['user-reports']))
                        <!-- Search Filter -->
                    <div class="row filter-row">
                        
                        <div class="col-sm-6 col-md-3">  
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <select class="form-control floating select">
                                        <option>
                                            Name1
                                        </option>
                                        <option>
                                            Name2
                                        </option>
                                    </select>
                                </div>
                                <label class="focus-label">User Role</label>
                            </div>
                        </div>
                    
                        <div class="col-sm-6 col-md-3">  
                            <a href="#" class="btn btn-success w-100"> Search </a>  
                        </div>     
                    </div>
                    <!-- /Search Filter -->
                    @endif