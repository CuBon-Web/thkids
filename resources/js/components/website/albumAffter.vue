<template>
  <div>
    <h3 class="page-title">Hình ảnh bàn giao</h3>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="form-group">
              <label>Danh sách ảnh</label>
              <ImageMulti v-model="images" :title="'album-affter'" />
            </div>
            <vs-button color="primary" @click="save">Lưu</vs-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import ImageMulti from '../_common/upload_image_multi';

export default {
  name: 'albumAffter',
  data() {
    return {
      images: [],
    };
  },
  components: {
    ImageMulti,
  },
  methods: {
    ...mapActions(['saveAlbumAffter', 'listAlbumAffter', 'loadings']),
    load() {
      this.loadings(true);
      this.listAlbumAffter().then(response => {
        this.loadings(false);
        const data = response.data || [];
        this.images = data
          .map(item => item.image || item.after || item.before || '')
          .filter(Boolean);
      }).catch(() => { this.loadings(false); });
    },
    save() {
      this.loadings(true);
      const payload = (this.images || []).map((image, index) => ({
        image: image || '',
        title: '',
        status: 1,
        sort: index,
      }));

      this.saveAlbumAffter({ data: payload }).then(() => {
        this.loadings(false);
        this.$success('Lưu thành công');
      }).catch(() => {
        this.loadings(false);
        this.$error('Lưu thất bại');
      });
    },
  },
  mounted() {
    this.load();
  },
};
</script>
