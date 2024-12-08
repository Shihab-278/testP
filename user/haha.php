<!DOCTYPE html>
<!--[if lt IE 10]> <html  lang="en" class="iex"> <![endif]-->
<!--[if (gt IE 10)|!(IE)]><!-->
<html lang="en" class="no-js js">
<!--<![endif]-->

<head>

    <title>Place A New IMEI Order</title>
    <meta charset="UTF-8" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <script>
        var urlprefix = '';
    </script>

    <base href="https://shunlocker.com/" />

    <link rel="icon" href="images/gallery/favicon.png">

    <link rel="stylesheet" href="templates/default/css/bootstrap.min.css" />
    <link rel="stylesheet" href="templates/default/css/chosen.min.css" />

    <script src="templates/default/js/jquery-3.2.1.min.js"></script>
    <script src="templates/default/js/popper.min.js"></script>
    <script src="templates/default/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="templates/default/js/theme.js?28d6304dd6d05d172bd2c2ad4fe98d0bebabf4de"></script>
    <script type="text/javascript" src="includes/js/custom.js?28d6304dd6d05d172bd2c2ad4fe98d0bebabf4de"></script>
    <script src="templates/default/js/chosen.jquery.min.js"></script>
    <script src="templates/default/js/Chart.bundle.min.js"></script>
    <script src="templates/default/js/bootstrap-datepicker.min.js"></script>
    <script src="templates/default/js/jquery.lightSlider.min.js" type="text/javascript"></script>
    <script src="templates/default/js/table-cell-selector.js" type="text/javascript"></script>
    <script src="templates/default/js/wow.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="includes/js/imei.js"></script>

    <script type="text/javascript" src="templates/default/js/jquery.steps.min.js"></script>

    <link type="text/css" rel="stylesheet" href="templates/default/css/lightSlider.css" />
    <link rel="stylesheet" href="templates/default/css/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" href="templates/default/css/animate.min.css" />
    <link href="templates/default/css/typekit-offline.css" rel="stylesheet" />


    <link rel="stylesheet" href="templates/default/css/all.css" />
    <link href="includes/icons/menu-icon.css" rel="stylesheet" type="text/css" />
    <link href="includes/icons/flags/flags.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="templates/default/css/theme.css?228d6304dd6d05d172bd2c2ad4fe98d0bebabf4de" />



    <link rel="stylesheet" href="templates/default/css/themes/dark.css?28d6304dd6d05d172bd2c2ad4fe98d0bebabf4de783" />




</head>





<body class="svg-light default tpl-client_order_imei     page-resellerplaceorder  cart">








<!-- 1st one  place a imei order -->

    <div class="bg-gray " style="min-height: calc(100vh - 525px)">
        <div class="w-100    min-height">

        <!-- place a imei  -->
            <div class="bg-white py-2 py-lg-4  border-bottom mb-2 mb-lg-3">

                <div class="container px-3 px-lg-3 p-lg-0">
                    <h3 class="page-title m-0">
                        Place A New IMEI Order
                    </h3>
                </div>

                <div class="container d-none d-md-block">
                    <ul class="breadcrumb mt-1">
                        <i class="fal fa-home"></i> <a href="./main">Dashboard</a> / <a href='./resellerplaceorder/action/imei'>Place A New IMEI Order</a> /
                    </ul>
                </div>
            </div>
        <!-- end place a imei  -->


            <div class="body-container">

                <script type="text/javascript">
                    $(document).ready(function() {
                        $('.service-list').searchService({
                            searchBox: '#searchservicebox2'
                        });
                    });

                    function popupOrder(ServicesID, servicename) {
                        if (servicename != '') {
                            $('.servicename').text(servicename);
                        }
                        $('.service-title').text(servicename);
                        $('#service-id').val(ServicesID);

                        $('#modal-order').show();
                        getServicedetailsIMEI(ServicesID, '#serviceDetails2', '.alert', true, '#loader', '', '', '#information', 0);
                    }
                </script>




                <div class="container">
                    <div class="page-container">
                        <div>
                            <form method="post" name="imeiorder_form2" id="imeiorder_form" class="h-100">
                               


                                <div class="card  card-search mb-3 bottom-space">
                                    <div class="form-inline">

                                        <div class="form-group pr-3 d-none d-lg-block">
                                            <i class="fal fa-search"></i>
                                        </div>
                                        <div class="form-group w-25">
                                            <select class="form-control" onChange="loadItems(this.value)" style="min-width: 300px">
                                                <option value="">All Group</option>
                                                <option value="0">CANADA NETWORKS ip</option>
                                                <option value="1">iRemoval Pro Premium iPhone 3.0</option>
                                                
                                                

                                            </select>
                                        </div>


                                        <div class="custom-control custom-checkbox mr-3  mt-lg-0 ml-lg-auto" data-toggle="tooltip" data-title="Discounted services">
                                            <input type="checkbox" name="discounted" onclick="showdiscounted(this);" class="custom-control-input" id="chk1">
                                            <label class="custom-control-label" for="chk1"> Discounted </label>
                                        </div>



                                    </div>
                                </div>



                                <div class="h-100 d-flex w-100 flex-column">

                                    <div class="row">
                                        <!-- service list -->
                                        <div class="col-lg-4 col-left h-100 overflow" style="overflow: auto">

                                            <div class="service-list">
                                                <div class="card mb-4 bottom-space group active g_0">
                                                    <div>
                                                        <h4 class="pb-3 m-0 border-bottom">
                                                            CANADA NETWORKS ip
                                                        </h4>
                                                        <div class="cursor-pointer  service active " id="id_3d3d286a8d153a4a58156d0e02d8570c" onclick="popupOrder('3d3d286a8d153a4a58156d0e02d8570c',' CANADA SASKTEL IPHONE 4/5/5S/5C/6/6+/6S/6S+/7/7+/8/8+ CLEAN IMEI ');">
                                                            <div class="border-top w-100">
                                                                <div class="">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-lg-9">
                                                                            <div class="py-3">
                                                                                <span class="searchme"> CANADA SASKTEL IPHONE 4/5/5S/5C/6/6+/6S/6S+/7/7+/8/8+ CLEAN IMEI </span>
                                                                                <br />
                                                                                <span class="text-muted"> Miniutes </span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-3 text-right">
                                                                            <div class="py-3">
                                                                                <div>
                                                                                    <div class="no-wrap"> <span class="text-muted"> $ </span> 2677.5</div>
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
                                        <!--end service list -->

                                        <!-- main section -->
                                        <div class="col-lg-8 col-right d-flex h-100  flex-column">
                                            <div class="card card-servicedetail">
                                                <h4 class="mb-4 bottom-space service-title"> </h4>
                                                <input type="hidden" id="service-id" name="id" />
                                                <input type="hidden" id="service-name" name="servicename" />
                                                <div id="serviceDetails2" class="position-relative d-flex flex-column">
                                                    <div class="">
                                                        <h2 class="p-5 text-center mt-5" style="color:#ccc;font-weight:100;">
                                                            <i class="fal fa-arrow-left"></i>&nbsp;&nbsp;
                                                            <div>
                                                                Select Service
                                                            </div>
                                                        </h2>
                                                    </div>
                                                </div>
                                                <div id="loader2" style="display: none">
                                                    <div class="position-absolute d-flex justify-content-center align-items-center w-100 h-100 text-center" style="top:0;left:0;">
                                                        <div class="spinner-loader"><i class="fal fa-circle-notch fa-spin fa-3x fa-fw"></i></div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <!--end main section -->

                                    </div>
                                   
                                </div>

                            </form>
                        </div>
                    </div>
                </div>



                <style>
                    .list-group-item:hover {
                        cursor: pointer;
                        background: #eee
                    }

                    .group,
                    .service {
                        display: none
                    }

                    .group.active,
                    .service.active {
                        display: flex
                    }

                    ::-webkit-scrollbar {
                        width: 2px;
                    }

                    ::-webkit-scrollbar-track {
                        background: #f1f1f1;
                    }

                    ::-webkit-scrollbar-thumb {
                        background: #888;
                    }

                    ::-webkit-scrollbar-thumb:hover {
                        background: #555;
                    }
                </style>

                
            </div>
        </div>
    </div>
  
<!-- end 1st one  place a imei order -->





</body>

</html>
