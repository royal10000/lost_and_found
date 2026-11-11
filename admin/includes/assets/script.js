async function updateStatus(element, options = {}) {
  const id = element.dataset.id;
  const status = element.value;
  const row = element.closest(options.rowSelector || "tr");

  try {
    const response = await fetch(options.url || window.location.href, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        changeReportStatus: 1,
        id: id,
        status: status,
      }),
    });

    const result = await response.json();

    if (result.success) {
      if (options.removeOnSuccess && row) {
        row.remove();
      }

      if (options.onSuccess) {
        options.onSuccess(result, row);
      }
    } else {
      alert(options.errorMessage || "Update failed");
    }
  } catch (error) {
    console.error(error);
  }
}
