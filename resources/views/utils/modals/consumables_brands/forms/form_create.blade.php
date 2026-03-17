  <form id="formStoreBrand" method="POST">
      @csrf
      <div class="row">
          <div class="form-group">
              <label for="name_mdlcbrand" class="form-label">Nombre <span>*</span></label>
              <input required id="name_mdlcbrand" name="name_mdlcbrand" type="text" class="form-control inputName input-fill"
                  placeholder="Nombre" maxlength="160">
              <p class="msgError name_mdlcbrand_error" style="font-weight: bold;color:rgb(208, 11, 11);"></p>
          </div>
      </div>
  </form>
