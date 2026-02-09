document.addEventListener("DOMContentLoaded", function () {
  // 1. Definition of Classes
  const tabs = document.querySelectorAll(".tab-button");
  const contents = document.querySelectorAll(".tab-content");

  // Classes for the Active Tab (Selected)
  const activeClasses = ["bg-blue-50", "text-blue-600", "border-blue-600"];
  // Classes for Inactive Tabs
  const inactiveClasses = ["border-transparent", "text-slate-600"];

  function activateTab(targetId) {
    // A. Reset all tabs to Inactive
    tabs.forEach((tab) => {
      tab.classList.remove(...activeClasses);
      tab.classList.add(...inactiveClasses);
    });

    // B. Hide all contents
    contents.forEach((content) => {
      content.classList.add("hidden");
    });

    // C. Activate clicked tab
    const activeTab = document.querySelector(
      `.tab-button[data-target="${targetId}"]`,
    );
    if (activeTab) {
      activeTab.classList.remove(...inactiveClasses);
      activeTab.classList.add(...activeClasses);
    }

    // D. Show content
    const targetContent = document.getElementById(targetId);
    if (targetContent) {
      targetContent.classList.remove("hidden");
    }

    // E. Save to Storage
    localStorage.setItem("mtm_active_tab_v2", targetId);
  }

  // 2. Click Event Listeners
  tabs.forEach((tab) => {
    tab.addEventListener("click", function () {
      const targetId = this.getAttribute("data-target");
      activateTab(targetId);
    });
  });

  // 3. Restore State on Load
  const lastTab = localStorage.getItem("mtm_active_tab_v2");
  if (lastTab) {
    activateTab(lastTab);
  } else {
    // Default to first tab if no history
    activateTab("section-license");
  }

  // 4. Initialize Bootstrap Popovers (WHMCS Native)
  if (typeof $ !== "undefined" && $.fn.popover) {
    $('[data-toggle="popover"]').popover({
      trigger: "hover",
      placement: "top",
      html: true,
    });
  }
});
