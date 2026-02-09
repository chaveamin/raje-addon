class RajeMediaManager {
  constructor() {
    this.activeInput = null;
    this.activePreview = null;
    this.currentTab = "uploaded";
    this.pendingDelete = { filename: null, tab: null };
    this.init();
  }

  init() {
    if (!document.getElementById("raje-media-modal")) {
      this.createModal();
      this.attachEventListeners();
    }
    this.bindTriggerButtons();
  }

  createModal() {
    let mod_dir = `../../../../../raje/modules/addons/raje`;

    const modalHtml = `
        <div dir="rtl" id="raje-media-modal" class="hidden fixed inset-0 z-50 overflow-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-5/6 rounded-3xl shadow-3xl flex flex-col h-[95vh] overflow-hidden animate-fade-in-up relative">
                <div class="flex justify-between items-center px-8 pt-4 bg-white z-10 relative">
                    <div class="flex items-center justify-between w-full">
                        <h3 class="!text-lg !font-bold !text-zinc-800 !m-0">انتخاب تصویر</h3>
                        <div class="flex items-center gap-x-4 *:p-4 *:rounded-xl *:hover:bg-black/10 transition-colors">
                          <button type="button" id="refresh-media-btn" title="تازه سازی لیست تصاویر">
                              <img src='${mod_dir}/assets/img/refresh.svg' />
                          </button>
                          <button type="button" id="sync-media-btn" title="هماهنگ سازی تصاویر">
                              <img src='${mod_dir}/assets/img/sync-media.svg' />
                          </button>
                        </div>
                    </div>
                </div>

                <div class="flex gap-6 px-6 py-3 overflow-x-auto z-10 relative *:rounded-xl">
                    <button class="media-tab px-6 py-3 text-sm font-semibold text-zinc-600 hover:!bg-zinc-50 hover:!text-zinc-600 transition-colors" data-tab="uploaded">Uploaded</button>
                    <button class="media-tab px-6 py-3 text-sm font-semibold text-zinc-600 hover:!bg-zinc-50 hover:!text-zinc-600 transition-colors" data-tab="photos">Photos</button>
                    <button class="media-tab px-6 py-3 text-sm font-semibold text-zinc-600 hover:!bg-zinc-50 hover:!text-zinc-600 transition-colors" data-tab="illustrations">Illustrations</button>
                    <button class="media-tab px-6 py-3 text-sm font-semibold text-zinc-600 hover:!bg-zinc-50 hover:!text-zinc-600 transition-colors" data-tab="icons">Icons</button>
                    <button class="media-tab px-6 py-3 text-sm font-semibold text-zinc-600 hover:!bg-zinc-50 hover:!text-zinc-600 transition-colors ml-auto" data-tab="upload_new"><i class="fas fa-upload mr-2"></i> Upload New</button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 bg-zinc-50 relative z-0 rounded-3xl w-[98%] mx-auto">
                    <div class="relative group mb-6">
                        <input type="text" id="media-search-input" placeholder="جستجو..." class="bg-zinc-100 outline-0 focus:ring-2 focus:ring-zinc-300 p-5 rounded-xl text-sm text-zinc-800 transition-all w-full">
                    </div>

                    <div id="media-grid" class="grid gap-4"></div>
                    
                    <div id="upload-area" class="hidden flex flex-col items-center justify-center h-full border-2 border-dashed border-zinc-300 rounded-xl bg-white m-4">
                        <i class="fas fa-cloud-upload-alt text-6xl text-zinc-300 mb-4"></i>
                        <p class="text-zinc-500 mb-4 font-bold">Drag and drop files here or click to upload</p>
                        <p class="text-xs text-zinc-400 mb-6">Allowed: JPG, PNG, SVG, WEBP</p>
                        <input type="file" id="file-upload-input" class="hidden" accept=".jpg,.jpeg,.png,.svg,.webp">
                        <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all" onclick="document.getElementById('file-upload-input').click()">Select Files</button>
                    </div>

                    <div id="media-loader" class="hidden absolute inset-0 bg-white/80 flex items-center justify-center z-50 backdrop-blur-[1px]">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-2"></i>
                            <span class="text-sm text-zinc-500 font-bold">Loading...</span>
                        </div>
                    </div>
                    <button type="button" class="close-modal text-zinc-400 hover:text-red-500 text-2xl px-2 transition-colors absolute right-0 bottom-0">close</button>
                </div>

                <div id="raje-delete-confirm" class="hidden absolute inset-0 bg-white/90 backdrop-blur-sm flex items-center justify-center p-4 transition-all" style="z-index: 9999;">
                    <div class="bg-white rounded-2xl shadow-2xl border border-zinc-100 p-8 w-full max-w-sm text-center transform scale-100 animate-bounce-in ring-1 ring-zinc-200">
                        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5">
                            <i class="fas fa-trash-alt text-2xl"></i>
                        </div>
                        <h3 class="!text-xl !font-bold !text-zinc-800 !mb-2">Delete File?</h3>
                        <p class="text-zinc-500 text-sm mb-8 leading-relaxed">
                            Are you sure you want to permanently delete <br>
                            <span id="del-filename-display" class="font-mono font-bold text-zinc-700 bg-zinc-100 px-2 py-1 rounded text-xs break-all"></span>?
                            <br><span class="text-red-400 text-xs mt-1 block">This action cannot be undone.</span>
                        </p>
                        
                        <div class="flex gap-3 justify-center">
                            <button id="cancel-delete-btn" class="flex-1 px-5 py-3 border border-zinc-200 rounded-xl text-zinc-600 hover:bg-zinc-50 hover:border-zinc-300 font-bold transition-all">
                                Cancel
                            </button>
                            <button id="confirm-delete-btn" class="flex-1 px-5 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl shadow-lg shadow-red-500/30 font-bold transition-all flex items-center justify-center gap-2">
                                <span>Delete</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>`;
    document.body.insertAdjacentHTML("beforeend", modalHtml);
  }

  attachEventListeners() {
    const modal = document.getElementById("raje-media-modal");

    modal.querySelectorAll(".close-modal").forEach((btn) => {
      btn.onclick = () => modal.classList.add("hidden");
    });

    modal.querySelectorAll(".media-tab").forEach((btn) => {
      btn.onclick = (e) => {
        const tabBtn = e.currentTarget;
        this.currentTab = tabBtn.dataset.tab || "uploaded";

        // Clear Search on tab switch
        const searchInput = document.getElementById("media-search-input");
        if (searchInput) searchInput.value = "";

        modal.querySelectorAll(".media-tab").forEach((t) => {
          t.classList.remove("bg-black/5");
          t.classList.add("border-transparent");
        });
        tabBtn.classList.add("bg-black/5");
        tabBtn.classList.remove("border-transparent");

        if (this.currentTab === "upload_new") {
          document.getElementById("media-grid").classList.add("hidden");
          document.getElementById("upload-area").classList.remove("hidden");
        } else {
          document.getElementById("media-grid").classList.remove("hidden");
          document.getElementById("upload-area").classList.add("hidden");
          this.loadMedia(this.currentTab);
        }
      };
    });

    // SEARCH LISTENER
    const searchInput = document.getElementById("media-search-input");
    if (searchInput) {
      searchInput.addEventListener("keyup", (e) => {
        const term = e.target.value.toLowerCase();
        const items = document.querySelectorAll("#media-grid > div");

        items.forEach((item) => {
          // We add 'media-name' class to the text div in loadMedia
          const nameDiv = item.querySelector(".media-name");
          const name = nameDiv ? nameDiv.textContent.toLowerCase() : "";

          if (name.includes(term)) {
            item.classList.remove("hidden");
          } else {
            item.classList.add("hidden");
          }
        });
      });
    }

    document.getElementById("refresh-media-btn").onclick = () => {
      // Clear search on refresh
      const s = document.getElementById("media-search-input");
      if (s) s.value = "";

      if (this.currentTab && this.currentTab !== "upload_new")
        this.loadMedia(this.currentTab);
      else if (!this.currentTab) this.loadMedia("uploaded");
    };

    document.getElementById("sync-media-btn").onclick = () => {
      this.syncDefaults();
    };

    document.getElementById("file-upload-input").onchange = (e) =>
      this.uploadFile(e.target.files[0]);

    document.getElementById("cancel-delete-btn").onclick = () => {
      document.getElementById("raje-delete-confirm").classList.add("hidden");
      this.pendingDelete = { filename: null, tab: null };
    };

    document.getElementById("confirm-delete-btn").onclick = () => {
      this.executeDelete();
    };
  }

  bindTriggerButtons() {
    document.querySelectorAll(".media-select-btn").forEach((btn) => {
      btn.onclick = (e) => {
        e.preventDefault();
        const wrapper = btn.closest(".media-input-group");
        this.activeInput = wrapper.querySelector('input[type="text"]');
        this.activePreview = wrapper.querySelector(".preview-img");

        document.getElementById("raje-media-modal").classList.remove("hidden");
        document.querySelector('.media-tab[data-tab="uploaded"]').click();
      };
    });
  }

  deleteFile(filename, tab) {
    this.pendingDelete = { filename: filename, tab: tab };
    document.getElementById("del-filename-display").textContent = filename;
    document.getElementById("raje-delete-confirm").classList.remove("hidden");
  }

  async executeDelete() {
    const { filename, tab } = this.pendingDelete;
    if (!filename) return;

    const btn = document.getElementById("confirm-delete-btn");
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    try {
      const formData = new FormData();
      formData.append("media_action", "delete_file");
      formData.append("file", filename);
      formData.append("tab", tab);

      let baseUrl = window.location.href.split("#")[0];
      const separator = baseUrl.includes("?") ? "&" : "?";
      const freshUrl = `${baseUrl}${separator}nocache=${new Date().getTime()}`;

      const response = await fetch(freshUrl, {
        method: "POST",
        body: formData,
        cache: "no-store",
        credentials: "same-origin",
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });
      const data = await response.json();

      if (data.status === "success") {
        this.loadMedia(this.currentTab);
        document.getElementById("raje-delete-confirm").classList.add("hidden");
      } else {
        alert(data.msg);
      }
    } catch (err) {
      console.error(err);
      alert("Delete failed.");
    }

    btn.innerHTML = originalText;
    btn.disabled = false;
    this.pendingDelete = { filename: null, tab: null };
  }

  async syncDefaults() {
    if (!confirm("Sync will copy new files from defaults. Continue?")) return;
    this.toggleLoader(true);
    try {
      const formData = new FormData();
      formData.append("media_action", "sync_defaults");
      let baseUrl = window.location.href.split("#")[0];
      const separator = baseUrl.includes("?") ? "&" : "?";
      const freshUrl = `${baseUrl}${separator}nocache=${new Date().getTime()}`;
      const response = await fetch(freshUrl, {
        method: "POST",
        body: formData,
        cache: "no-store",
        credentials: "same-origin",
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });
      const data = await response.json();
      if (data.status === "success") {
        alert(data.msg);
        this.loadMedia(
          this.currentTab && this.currentTab !== "upload_new"
            ? this.currentTab
            : "uploaded",
        );
      } else {
        alert("Sync failed.");
      }
    } catch (err) {
      console.error(err);
    }
    this.toggleLoader(false);
  }

  async loadMedia(tab) {
    this.toggleLoader(true);

    // --- DYNAMIC SIZING ---
    const grid = document.getElementById("media-grid");
    // Reset base classes
    grid.className = "grid gap-4";

    if (tab === "icons") {
      // Smaller items: more columns (4 on mobile, 8 on md, 10 on lg)
      grid.classList.add("grid-cols-4", "md:grid-cols-8", "lg:grid-cols-16");
    } else {
      // Bigger items: fewer columns (2 on mobile, 3 on md, 4 on lg)
      grid.classList.add("grid-cols-2", "md:grid-cols-3", "lg:grid-cols-4");
    }

    try {
      const formData = new FormData();
      formData.append("media_action", "list_media");
      formData.append("tab", tab);

      let baseUrl = window.location.href.split("#")[0];
      const separator = baseUrl.includes("?") ? "&" : "?";
      const freshUrl = `${baseUrl}${separator}nocache=${new Date().getTime()}`;

      const response = await fetch(freshUrl, {
        method: "POST",
        body: formData,
        cache: "no-store",
        credentials: "same-origin",
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });
      const text = await response.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        console.error(text);
      }

      grid.innerHTML = "";

      if (data && data.files && data.files.length > 0) {
        data.files.forEach((file) => {
          const div = document.createElement("div");
          // NOTE: Added "media-name" class to the label for the search function
          div.className =
            "media group relative aspect-square bg-white border border-zinc-200 hover:shadow-lg cursor-pointer transition-shadow hover:ring hover:ring-zinc-400";

          div.innerHTML = `
                        <button type="button" class="delete-media-btn absolute -top-2 -right-2 bg-red-100 text-white size-12 rounded-2xl flex items-center justify-center opacity-0 group-hover:opacity-100 z-20 hover:bg-red-600 transition-all shadow-md transform scale-90 group-hover:scale-100" title="Delete">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                        <img src="${file.url}" class=" object-contain p-2" loading="lazy">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors"></div>
                        <div class="media-name absolute bottom-0 left-0 right-0 bg-white/80 text-[10px] text-zinc-600 p-1 truncate text-center font-mono">${file.name}</div>
                    `;

          div.onclick = () => this.selectImage(file.path, file.url);

          const delBtn = div.querySelector(".delete-media-btn");
          delBtn.onclick = (e) => {
            e.stopPropagation();
            this.deleteFile(file.name, tab);
          };

          grid.appendChild(div);
        });
      } else {
        grid.innerHTML =
          '<div class="col-span-full text-center text-zinc-400 py-10 flex flex-col items-center"><i class="fas fa-folder-open text-4xl mb-3 opacity-30"></i><span>No images found.</span></div>';
      }
    } catch (err) {
      console.error(err);
    }
    this.toggleLoader(false);

    const media = document.querySelectorAll(".media");
    if (tab === "icons") {
      media.forEach((m) => {
        const mediaImg = m.querySelector("img");
        const mediaName = m.querySelector(".media-name");
        m.classList.add("rounded-xl", "flex", "items-center", "justify-center");
        mediaImg.classList.add("size-3/5");
        mediaName.classList.add("hidden");
      });
    } else {
      media.forEach((m) => {
        const mediaImg = m.querySelector("img");
        const mediaName = m.querySelector(".media-name");
        m.classList.add("rounded-2xl");
        mediaImg.classList.add("size-full");
        mediaName.classList.add("block");
      });
    }
  }

  async uploadFile(file) {
    if (!file) return;
    this.toggleLoader(true);
    const formData = new FormData();
    formData.append("media_action", "upload_file");
    formData.append("file", file);

    let baseUrl = window.location.href.split("#")[0];
    const separator = baseUrl.includes("?") ? "&" : "?";
    const freshUrl = `${baseUrl}${separator}nocache=${new Date().getTime()}`;

    try {
      const response = await fetch(freshUrl, {
        method: "POST",
        body: formData,
        cache: "no-store",
        credentials: "same-origin",
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });
      const data = await response.json();
      if (data.status === "success") {
        document.querySelector('.media-tab[data-tab="uploaded"]').click();
      } else {
        alert(data.msg);
      }
    } catch (err) {
      console.error(err);
      alert("Upload failed");
    }
    this.toggleLoader(false);
  }

  selectImage(path, url) {
    if (this.activeInput) {
      this.activeInput.value = path;
      this.activeInput.dispatchEvent(new Event("change"));
    }
    if (this.activePreview) {
      this.activePreview.src = url;
      this.activePreview.classList.remove("hidden");
      const placeholder = this.activePreview.parentElement.querySelector(
        ".preview-placeholder",
      );
      if (placeholder) placeholder.classList.add("hidden");
    }
    document.getElementById("raje-media-modal").classList.add("hidden");
  }

  toggleLoader(show) {
    const loader = document.getElementById("media-loader");
    if (show) loader.classList.remove("hidden");
    else loader.classList.add("hidden");
  }
}

document.addEventListener("DOMContentLoaded", () => {
  new RajeMediaManager();
});
