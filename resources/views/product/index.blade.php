<x-app-layout>
     
<div class="container">
    <div class="card mt-5">
        <h2 class="card-header"><i class="fa-regular fa-credit-card"></i> Product List</h2>
        <div class="card-body">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
                <a class="btn btn-success btn-sm" href="javascript:void(0)" id="createNewProduct"> <i class="fa fa-plus"></i> Create New Product</a>
            </div>

            <table class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th width="60px">No</th>
                        <th>Product Image</th>
                        <th>Category</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th width="280px">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    
</div>
     
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
            </div>
            <div class="modal-body">
                <form id="productForm" name="productForm" class="form-horizontal" enctype='multipart/form-data'>
                   <input type="hidden" name="product_id" id="product_id">
                   @csrf

                    <div class="alert alert-danger print-error-msg" style="display:none">
                        <ul></ul>
                    </div>
                    <div class="form-group mb-2">
                        <label for="name" class="col-sm-12 control-label">Category:</label>
                        <div class="col-sm-12">
                            <select   class="form-control" id="cat_id" name="cat_id" placeholder="cat_id" value=""  >
                                    @foreach($category as $r)
                                        <option value="{{$r->id}}">{{$r->name}}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
       

                    <div class="form-group mb-2">
                        <label for="name" class="col-sm-12 control-label">Product Name:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="product_name" placeholder="Enter Product Name" value="" maxlength="50">
                        </div>
                    </div>
       
                    <div class="form-group mb-2">
                        <label class="col-sm-12 control-label">Price:</label>
                        <div class="col-sm-12">
                            <input id="price" type="text" name="price" placeholder="Enter Price" class="form-control"> 
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="col-sm-12 control-label">Phone Number:</label>
                        <div class="col-sm-12">
                            <input id="phone_number" type="text"  name="phone_number" placeholder="Enter Phone Number" class="form-control"> 
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="col-sm-12 control-label">Product Description:</label>
                        <div class="col-sm-12">
                            <input id="product_description" type="text" name="product_description" placeholder="Enter Product Description" class="form-control"> 
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="col-sm-12 control-label">Image:</label>
                        <div class="col-sm-12">
                            <input id="product_image" type="file" name="product_image" placeholder="Enter Product Image" class="form-control border"> 
                        </div>
                        <p class="product-imagess"><strong>Product Image:</strong></p> <img class="product-images" src="" style="height:100px">
 
                    </div>
                    <div class="col-sm-offset-2 col-sm-10">
                     <button type="submit" class="btn btn-success mt-2" id="saveBtn" value="create"><i class="fa fa-save"></i> Submit
                     </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="showModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"><i class="fa-regular fa-eye"></i> Show Product</h4>
            </div>
            <div class="modal-body">
                <p><strong>Product Name:</strong> <span class="show-name"></span></p>
                <p><strong>Product Price:</strong> <span class="show-price"></span></p>
                <p><strong>Product Price:</strong> <span class="show-price"></span></p>
                <p><strong>Phone Number:</strong> <span class="phone-number"></span></p>
                <p><strong>Product Description:</strong> <span class="product-description"></span></p>
                <p><strong>Product Image:</strong></p> <img class="product-image" src="" style="height:50px">
 
            </div>
        </div>
    </div>
</div>
       
      
<script type="text/javascript">
  $(function () {

    /*------------------------------------------
     --------------------------------------------
     Pass Header Token
     --------------------------------------------
     --------------------------------------------*/ 
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
      
    /*------------------------------------------
    --------------------------------------------
    Render DataTable
    --------------------------------------------
    --------------------------------------------*/
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('products.index') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'product_image', name: 'product_image',
                "render": function (data, type, full, meta) {
                    var datas=data.replace('public/',''); 
                    //alert(datas);
                        return "<img src=\"storage/" + datas + "\" style=\"height:50px\"/>";
                    },  
            },
             
            {data: 'cat_id', name: 'cat_id'},
            {data: 'product_name', name: 'product_name'},
            {data: 'price', name: 'price'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
      
    /*------------------------------------------
    --------------------------------------------
    Click to Button
    --------------------------------------------
    --------------------------------------------*/
    $('#createNewProduct').click(function () {
        $('#saveBtn').val("create-product");
        $('#product_id').val('');
        $('#productForm').trigger("reset");
        $('#modelHeading').html("<i class='fa fa-plus'></i> Create New Product");
        $('#ajaxModel').modal('show');
        $('.product-images').hide();
        $('.product-imagess').hide();
    });

    /*------------------------------------------
    --------------------------------------------
    Click to Edit Button
    --------------------------------------------
    --------------------------------------------*/
    $('body').on('click', '.showProduct', function () {
      var product_id = $(this).data('id');
      $.get("{{ route('products.index') }}" +'/' + product_id, function (data) {
          $('#showModel').modal('show');
          $('.show-name').text(data.product_name);
          $('.show-price').text(data.price);
          $('.phone-number').text(data.phone_number);
          
          $('.product-description').text(data.product_description);
          var datas=data.product_image.replace('public/',''); 
          $('.product-image').attr("src", "storage/"+ datas);
          
      })
    });
      
    /*------------------------------------------
    --------------------------------------------
    Click to Edit Button
    --------------------------------------------
    --------------------------------------------*/
    $('body').on('click', '.editProduct', function () {
      var product_id = $(this).data('id');
      $.get("{{ route('products.index') }}" +'/' + product_id +'/edit', function (data) {
          $('#modelHeading').html("<i class='fa-regular fa-pen-to-square'></i> Edit Product");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal('show');
          $('#product_id').val(data.id);
          $('#cat_id').val(data.cat_id).prop("selected", true);;
          $('#name').val(data.product_name);
          $('#price').val(data.price);
          $('#phone_number').val(data.phone_number);
          $('#product_description').val(data.product_description);
          var datas=data.product_image.replace('public/',''); 
          $('.product-images').attr("src", "storage/"+ datas);
          $('.product-images').show();
          $('.product-imagess').show();
         
      })
    });
      
    /*------------------------------------------
    --------------------------------------------
    Create Product Code
    --------------------------------------------
    --------------------------------------------*/
    $('#productForm').submit(function(e) {
        e.preventDefault();
 
        let formData = new FormData(this);
        $('#saveBtn').html('Sending...');
  
        $.ajax({
                type:'POST',
                url: "{{ route('products.store') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: (response) => {
                      $('#saveBtn').html('Submit');
                      $('#productForm').trigger("reset");
                      $('#ajaxModel').modal('hide');
                      table.draw();
                },
                error: function(response){
                    $('#saveBtn').html('Submit');
                    $('#productForm').find(".print-error-msg").find("ul").html('');
                    $('#productForm').find(".print-error-msg").css('display','block');
                    $.each( response.responseJSON.errors, function( key, value ) {
                        $('#productForm').find(".print-error-msg").find("ul").append('<li>'+value+'</li>');
                    });
                }
           });
      
    });
      
    /*------------------------------------------
    --------------------------------------------
    Delete Product Code
    --------------------------------------------
    --------------------------------------------*/
    $('body').on('click', '.deleteProduct', function () {
     
        var product_id = $(this).data("id");
        confirm("Are You sure want to delete?");
        
        $.ajax({
            type: "DELETE",
            url: "{{ route('products.store') }}"+'/'+product_id,
            success: function (data) {
                table.draw();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
       
  });
</script>
<script>
         function updatemessage(id,status) {
             
            $.ajax({
               type:'Get',
               url:'/product-update/'+id+'/'+status,
               
                 success:function(data) {
                    $('.data-table').DataTable().ajax.reload();
                 //   alert(data.msg);
               }
            });
         }
      </script>
 
</x-app-layout>
     
