<!doctype html>
<html lang="en">
   
<head>
    @include('components.backend.head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.js"></script>
</head>
     
    @include('components.backend.header')
 
      <!--start sidebar wrapper-->  
      @include('components.backend.sidebar')
     <!--end sidebar wrapper-->
 
 
 
       <div class="page-body">
          <div class="container-fluid">            
            <div class="page-title">
              <div class="row">
 
                  <div class="col-xl-3 col-sm-6">
                    <div class="card o-hidden small-widget">
                      <div class="card-body total-project border-b-primary border-2"><span class="f-light f-w-500 f-14">Total Project</span>
                        <div class="project-details">
                          <div class="project-counter">
                            <h2 class="f-w-600">1,523</h2><span class="f-12 f-w-400">(This month)</span>
                          </div>
                          <div class="product-sub bg-primary-light">
                            <svg class="invoice-icon">
                              <use href="../assets/svg/icon-sprite.svg#color-swatch"></use>
                            </svg>
                          </div>
                        </div>
                        <ul class="bubbles">
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-3 col-sm-6">
                    <div class="card o-hidden small-widget">
                      <div class="card-body total-Progress border-b-warning border-2"> <span class="f-light f-w-500 f-14">In Progress</span>
                        <div class="project-details">
                          <div class="project-counter">
                            <h2 class="f-w-600">836</h2><span class="f-12 f-w-400">(This month) </span>
                          </div>
                          <div class="product-sub bg-warning-light">
                            <svg class="invoice-icon">
                              <use href="../assets/svg/icon-sprite.svg#tick-circle"></use>
                            </svg>
                          </div>
                        </div>
                        <ul class="bubbles">
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-3 col-sm-6">
                    <div class="card o-hidden small-widget">
                      <div class="card-body total-Complete border-b-secondary border-2"><span class="f-light f-w-500 f-14">Complete</span>
                        <div class="project-details">
                          <div class="project-counter">
                            <h2 class="f-w-600">475</h2><span class="f-12 f-w-400">(This month) </span>
                          </div>
                          <div class="product-sub bg-secondary-light">
                            <svg class="invoice-icon">
                              <use href="../assets/svg/icon-sprite.svg#add-square"></use>
                            </svg>
                          </div>
                        </div>
                        <ul class="bubbles">
                          <li class="bubble"> </li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"> </li>
                          <li class="bubble"></li>
                          <li class="bubble"> </li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"> </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-3 col-sm-6">
                    <div class="card o-hidden small-widget">
                      <div class="card-body total-upcoming"><span class="f-light f-w-500 f-14">Upcoming</span>
                        <div class="project-details">
                          <div class="project-counter">
                            <h2 class="f-w-600">189</h2><span class="f-12 f-w-400">(This month) </span>
                          </div>
                          <div class="product-sub bg-light-light">
                            <svg class="invoice-icon">
                              <use href="../assets/svg/icon-sprite.svg#edit-2"></use>
                            </svg>
                          </div>
                        </div>
                        <ul class="bubbles">
                          <li class="bubble"> </li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                          <li class="bubble"></li>
                        </ul>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
 
 
       
        </div>
        <!-- footer start-->
        @include('components.backend.footer')
        </div>
 
   
    @include('components.backend.main-js')
 
   
 
 
 
 
 
 
 
 
 
       
</body>
 
</html>
 