<!doctype html>
<html lang="en">
    
<head>
    @include('components.backend.head')
</head>
	   
		@include('components.backend.header')

	    <!--start sidebar wrapper-->	
	    @include('components.backend.sidebar')
	   <!--end sidebar wrapper-->


        <div class="page-body">
          <div class="container-fluid">
            <div class="page-title">
              <div class="row">
                <div class="col-6">
                  <h4>Add Banner Details Form</h4>
                </div>
                <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                    <a href="{{ route('banner-details.index') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Add Banner Details</li>
                </ol>

                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                    <div class="card-header">
                        <h4>Banner Details Form</h4>
                        <p class="f-m-light mt-1">Fill up your true details and submit the form.</p>
                    </div>
                    <div class="card-body">
                        <div class="vertical-main-wizard">
                        <div class="row g-3">    
                            <!-- Removed empty col div -->
                            <div class="col-12">
                            <div class="tab-content" id="wizard-tabContent">
                                <div class="tab-pane fade show active" id="wizard-contact" role="tabpanel" aria-labelledby="wizard-contact-tab">
                               
                                    <form class="row g-3 needs-validation custom-input" novalidate action="{{ route('banner-details.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf

                                        <!-- Banner Heading-->
                                        <div class="col-md-6">
                                            <label class="form-label" for="banner_heading">Banner Heading <span class="txt-danger">*</span> </label>
                                            <input class="form-control" id="banner_heading" type="text" name="banner_heading" placeholder="Enter Banner Heading" >
                                            <div class="invalid-feedback">Please enter a Banner Heading.</div>
                                        </div>


                                        <!-- Banner Title-->
                                        <div class="col-md-6">
                                            <label class="form-label" for="banner_title">Banner Title </label>
                                            <input class="form-control" id="banner_title" type="text" name="banner_title" placeholder="Enter Banner Title" >
                                            <div class="invalid-feedback">Please enter a Banner Title.</div>
                                        </div>

                                    

                                        <!-- Banner Media -->
                                        <div class="col-md-6 mt-5">
                                            <label class="form-label" for="banner_media">
                                                Banner Image / Video <span class="txt-danger">*</span>
                                            </label>

                                            <input
                                                class="form-control"
                                                id="banner_media"
                                                type="file"
                                                name="banner_media"
                                                accept=".jpg,.jpeg,.png,.webp,.mp4,.webm"
                                                
                                                onchange="previewBannerMedia()"
                                            >

                                            <div class="invalid-feedback">
                                                Please upload a banner image or video.
                                            </div>

                                            <small class="text-secondary d-block mt-1">
                                                <b>Allowed:</b> jpg, jpeg, png, webp, mp4, webm (Max 5MB)
                                            </small>

                                            <!-- Preview BELOW input -->
                                            <div id="bannerPreviewContainer" class="mt-3" style="display:none;">
                                                <img
                                                    id="banner_image_preview"
                                                    class="img-fluid"
                                                    style="max-height:200px; display:none; border:1px solid #ddd; padding:5px;"
                                                >

                                                <video
                                                    id="banner_video_preview"
                                                    controls
                                                    style="max-height:200px; display:none; border:1px solid #ddd; padding:5px;"
                                                ></video>
                                            </div>
                                        </div>



                                        <!-- Form Actions -->
                                        <div class="col-12 text-end">
                                            <a href="{{ route('banner-details.index') }}" class="btn btn-danger px-4">Cancel</a>
                                            <button class="btn btn-primary" type="submit">Submit</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                            </div>
                        </div>
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
        </div>



       
       @include('components.backend.main-js')

    <script>
        function previewBannerMedia() {
            const fileInput = document.getElementById('banner_media');
            const file = fileInput.files[0];

            const previewContainer = document.getElementById('bannerPreviewContainer');
            const imagePreview = document.getElementById('banner_image_preview');
            const videoPreview = document.getElementById('banner_video_preview');

            imagePreview.style.display = 'none';
            videoPreview.style.display = 'none';

            if (!file) return;

            const imageTypes = ['image/jpeg', 'image/png', 'image/webp'];
            const videoTypes = ['video/mp4', 'video/webm'];

            const url = URL.createObjectURL(file);

            if (imageTypes.includes(file.type)) {
                imagePreview.src = url;
                imagePreview.style.display = 'block';
            } else if (videoTypes.includes(file.type)) {
                videoPreview.src = url;
                videoPreview.style.display = 'block';
            } else {
                alert('Invalid file type');
                fileInput.value = '';
                return;
            }

            previewContainer.style.display = 'block';
        }
    </script>

</body>

</html>