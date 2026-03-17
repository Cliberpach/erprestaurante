  <form id="createCategoryForm" method="POST">
      @csrf
      <div class="row">
          <div class="form-group">
              <label for="name_mdlccategory" class="form-label">Nombre <span>*</span></label>
              <input required id="name_mdlccategory" name="name_mdlccategory" type="text" class="form-control inputName input-fill"
                  placeholder="Nombre" maxlength="160">
              <p class="msgError name_mdlccategory_error" style="font-weight: bold;color:rgb(208, 11, 11);"></p>
          </div>
      </div>
  </form>
