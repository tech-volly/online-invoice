<!-- Content Starts -->
                    <div class="card">
                        <div class="card-body">
                            <!-- <h4 class="card-title">Solid justified</h4> -->
                            <ul class="nav nav-tabs nav-tabs-solid nav-justified">
                                 @if(!Route::is(['archived-jobs','interviewing','job-aptitude','offered-jobs','questions','saved-jobs','user-all-jobs','user-dashboard','visited-jobs'])) 
                                <li class="nav-item"><a class="nav-link" href="{{url('user-dashboard')}}" >Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('user-all-jobs')}}">All </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('saved-jobs')}}">Saved</a></li>
                                <li class="nav-item"><a class="nav-link active" href="{{url('applied-jobs')}}">Applied</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('interviewing')}}" >Interviewing</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('offered-jobs')}}">Offered</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('visited-jobs')}}">Visitied </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('archived-jobs')}}">Archived </a></li>
                                @endif
                                @if(Route::is(['archived-jobs'])) 
                                <li class="nav-item"><a class="nav-link" href="{{url('user-dashboard')}}">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('user-all-jobs')}}" >All </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('saved-jobs')}}">Saved</a></li>
                                <li class="nav-item"><a class="nav-link " href="{{url('applied-jobs')}}">Applied</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('interviewing')}}">Interviewing</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('offered-jobs')}}">Offered</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('visited-jobs')}}">Visitied </a></li>
                                <li class="nav-item"><a class="nav-link active" href="{{url('archived-jobs')}}">Archived </a></li>
                                @endif
                                 @if(Route::is(['interviewing'])) 
                                <li class="nav-item"><a class="nav-link" href="{{url('user-dashboard')}}">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('user-all-jobs')}}">All </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('saved-jobs')}}">Saved</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('applied-jobs')}}">Applied</a></li>
                                <li class="nav-item"><a class="nav-link active" href="{{url('interviewing')}}">Interviewing</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('offered-jobs')}}">Offered</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('visited-jobs')}}">Visitied </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('archived-jobs')}}">Archived </a></li>
                                @endif
                                @if(Route::is(['job-aptitude']))
                                <li class="nav-item"><a class="nav-link" href="{{url('user-dashboard')}}">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('user-all-jobs')}}">All </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('saved-jobs')}}">Saved</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('applied-jobs')}}">Applied</a></li>
                                <li class="nav-item"><a class="nav-link active" href="{{url('interviewing')}}">Interviewing</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('offered-jobs')}}">Offered</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('visited-jobs')}}">Visitied </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('archived-jobs')}}">Archived </a></li>
                                @endif
                                 @if(Route::is(['offered-jobs']))
                                 <li class="nav-item"><a class="nav-link" href="{{url('user-dashboard')}}">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('user-all-jobs')}}">All </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('saved-jobs')}}">Saved</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('applied-jobs')}}">Applied</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('interviewing')}}">Interviewing</a></li>
                                <li class="nav-item"><a class="nav-link active" href="{{url('offered-jobs')}}">Offered</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('visited-jobs')}}">Visitied </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('archived-jobs')}}">Archived </a></li>
                                @endif
                                 @if(Route::is(['questions']))
                                 <li class="nav-item"><a class="nav-link" href="{{url('user-dashboard')}}">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('user-all-jobs')}}">All </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('saved-jobs')}}">Saved</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('applied-jobs')}}">Applied</a></li>
                                <li class="nav-item"><a class="nav-link active" href="{{url('interviewing')}}">Interviewing</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('offered-jobs')}}">Offered</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('visited-jobs')}}">Visitied </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('archived-jobs')}}">Archived </a></li>
                                @endif
                                @if(Route::is(['saved-jobs']))
                                <li class="nav-item"><a class="nav-link" href="{{url('user-dashboard')}}">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('user-all-jobs')}}">All </a></li>
                                <li class="nav-item"><a class="nav-link active" href="{{url('saved-jobs')}}">Saved</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('applied-jobs')}}">Applied</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('interviewing')}}">Interviewing</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('offered-jobs')}}">Offered</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('visited-jobs')}}">Visitied </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('archived-jobs')}}">Archived </a></li>
                                @endif
                                @if(Route::is(['user-all-jobs']))
                                 <li class="nav-item"><a class="nav-link" href="{{url('user-dashboard')}}">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link active" href="{{url('user-all-jobs')}}">All </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('saved-jobs')}}">Saved</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('applied-jobs')}}">Applied</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('interviewing')}}">Interviewing</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('offered-jobs')}}">Offered</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('visited-jobs')}}">Visitied </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('archived-jobs')}}">Archived </a></li>
                                @endif
                                @if(Route::is(['user-dashboard']))
                                 <li class="nav-item"><a class="nav-link active" href="{{url('user-dashboard')}}">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('user-all-jobs')}}">All </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('saved-jobs')}}">Saved</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('applied-jobs')}}">Applied</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('interviewing')}}">Interviewing</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('offered-jobs')}}">Offered</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('visited-jobs')}}">Visitied </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('archived-jobs')}}">Archived </a></li>
                                @endif
                                @if(Route::is(['visited-jobs']))
                                <li class="nav-item"><a class="nav-link" href="{{url('user-dashboard')}}">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('user-all-jobs')}}">All </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('saved-jobs')}}">Saved</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('applied-jobs')}}">Applied</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('interviewing')}}">Interviewing</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('offered-jobs')}}">Offered</a></li>
                                <li class="nav-item"><a class="nav-link active" href="{{url('visited-jobs')}}">Visitied </a></li>
                                <li class="nav-item"><a class="nav-link" href="{{url('archived-jobs')}}">Archived </a></li>
                                @endif
                            </ul>
                        </div>
                    </div> 