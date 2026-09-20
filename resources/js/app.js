import "./bootstrap";
import Alpine from "@alpinejs/csp";

Alpine.data("navigation", () => ({
  open: false,
  toggle() {
    this.open = !this.open;
  },
  close() {
    this.open = false;
  },
}));

Alpine.data("dashboard", () => ({
  collapsed: false,
  mobileOpen: false,
  opener: null,
  get shellClass() {
    return this.collapsed ? "is-collapsed" : "";
  },
  get sidebarClass() {
    return this.mobileOpen ? "is-open" : "";
  },
  get expanded() {
    return !this.collapsed;
  },
  init() {
    try {
      this.collapsed =
        localStorage.getItem("delsumart-sidebar") === "collapsed";
    } catch {}
    this.onKey = (event) => {
      if (!this.mobileOpen) return;
      if (event.key === "Escape") this.closeMobile();
      if (event.key === "Tab") {
        const items = [
          ...document.querySelectorAll(
            "#dashboard-sidebar a, #dashboard-sidebar button",
          ),
        ].filter((el) => el.offsetParent !== null);
        const first = items[0],
          last = items[items.length - 1];
        if (event.shiftKey && document.activeElement === first) {
          event.preventDefault();
          last.focus();
        }
        if (!event.shiftKey && document.activeElement === last) {
          event.preventDefault();
          first.focus();
        }
      }
    };
    this.onResize = () => {
      if (window.innerWidth >= 768 && this.mobileOpen) this.closeMobile();
      document.querySelector("#dashboard-sidebar").inert =
        window.innerWidth < 768 && !this.mobileOpen;
    };
    document.addEventListener("keydown", this.onKey);
    window.addEventListener("resize", this.onResize);
    this.onResize();
  },
  toggleCollapse() {
    this.collapsed = !this.collapsed;
    try {
      localStorage.setItem(
        "delsumart-sidebar",
        this.collapsed ? "collapsed" : "expanded",
      );
    } catch {}
  },
  openMobile() {
    this.opener = document.activeElement;
    this.mobileOpen = true;
    const sidebar = document.querySelector("#dashboard-sidebar");
    sidebar.inert = false;
    sidebar.setAttribute("role", "dialog");
    sidebar.setAttribute("aria-modal", "true");
    document.querySelector("#dashboard-workspace").inert = true;
    document.body.style.overflow = "hidden";
    this.$nextTick(() => sidebar.querySelector("button").focus());
  },
  closeMobile() {
    this.mobileOpen = false;
    const sidebar = document.querySelector("#dashboard-sidebar");
    sidebar.inert = window.innerWidth < 768;
    sidebar.removeAttribute("role");
    sidebar.removeAttribute("aria-modal");
    document.querySelector("#dashboard-workspace").inert = false;
    document.body.style.overflow = "";
    this.opener?.focus();
  },
  destroy() {
    document.removeEventListener("keydown", this.onKey);
    window.removeEventListener("resize", this.onResize);
  },
}));

Alpine.data("payoutBankSetup", () => ({
  banks: [],
  bankCode: "",
  accountNumber: "",
  resolved: null,
  loading: false,
  error: "",
  authorized: false,
  requestId: 0,
  get selectedBankName() {
    return this.banks.find((bank) => bank.code === this.bankCode)?.name || "";
  },
  get resolvedName() {
    return this.resolved?.account_name || "";
  },
  async init() {
    try {
      if (this.$el.dataset.bankAuthorized !== "true") return;
      const response = await fetch(this.$el.dataset.banksUrl, {
        headers: { Accept: "application/json" },
      });
      if (response.ok) {
        this.authorized = true;
        this.banks = (await response.json()).data;
      } else if (response.status !== 403)
        this.error = "Bank list is unavailable. Please refresh and try again.";
    } catch {
      this.error = "Unable to load banks. Check your connection and try again.";
    }
  },
  async maybeResolve() {
    const requestId = ++this.requestId;
    this.resolved = null;
    this.error = "";
    this.loading = false;
    if (!this.bankCode || !/^\d{10}$/.test(this.accountNumber)) return;
    this.loading = true;
    try {
      const response = await fetch(this.$el.dataset.resolveUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
            .content,
        },
        body: JSON.stringify({
          bank_code: this.bankCode,
          account_number: this.accountNumber,
        }),
      });
      const data = await response.json();
      if (requestId !== this.requestId) return;
      if (response.ok) this.resolved = data.data;
      else this.error = data.message || "Unable to verify this account.";
    } catch {
      if (requestId === this.requestId)
        this.error = "Unable to verify this account. Please try again.";
    } finally {
      if (requestId === this.requestId) this.loading = false;
    }
  },
}));

// Native dialogs retain keyboard focus and support Escape without weakening CSP.
document.addEventListener("click", (event) => {
  const trigger = event.target.closest("[data-dialog]");
  if (trigger) document.getElementById(trigger.dataset.dialog)?.showModal();
  const closer = event.target.closest("[data-close-dialog]");
  if (closer) closer.closest("dialog").close();
  const thumb = event.target.closest("[data-gallery-image]");
  if (thumb) {
    const gallery = thumb.closest("[data-gallery]");
    gallery.querySelector("[data-main-image]").src = thumb.dataset.galleryImage;
    gallery
      .querySelectorAll("[data-gallery-image]")
      .forEach((item) =>
        item.setAttribute("aria-pressed", String(item === thumb)),
      );
  }
});

document.querySelectorAll("[data-confirm]").forEach((form) => {
  form.addEventListener("submit", (event) => {
    if (form.dataset.confirmed === "true") return;
    event.preventDefault();
    const dialog = document.getElementById(form.dataset.confirm);
    dialog.querySelector("[data-confirm-action]").onclick = () => {
      form.dataset.confirmed = "true";
      dialog.close();
      form.requestSubmit(event.submitter);
    };
    dialog.showModal();
  });
});

document.querySelectorAll("[data-image-upload]").forEach((input) => {
  input.addEventListener("change", () => {
    const preview = document.getElementById(input.dataset.imageUpload);
    preview.replaceChildren();
    [...input.files].slice(0, 5).forEach((file) => {
      if (!file.type.startsWith("image/")) return;
      const image = new Image();
      image.alt = file.name;
      const reader = new FileReader();
      reader.onload = () => {
        image.src = reader.result;
      };
      reader.readAsDataURL(file);
      preview.append(image);
    });
  });
});
window.Alpine = Alpine;
Alpine.start();
