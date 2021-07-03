'use strict';

var $checkbox = document.getElementsByClassName('show_completed');

if ($checkbox.length) {
  $checkbox[0].addEventListener('change', function (event) {
    var is_checked = +event.target.checked;

    var searchParams = new URLSearchParams(window.location.search);
    searchParams.set('show_completed', is_checked);

    window.location = '/index.php?' + searchParams.toString();
  });
}

var $taskCheckboxes = document.getElementsByClassName('tasks');

if ($taskCheckboxes.length) {

  $taskCheckboxes[0].addEventListener('change', function (event) {
    if (event.target.classList.contains('task__checkbox')) {
      var el = event.target;

      var is_checked = +el.checked;
      var task_and_project_ids = el.getAttribute('value');
      var task_id = task_and_project_ids.slice(0, 1);

      if (task_and_project_ids.length === 2) {
        var project_id = task_and_project_ids.slice(-1);
        var url = '/index.php?project-id=' + project_id + '&task_id=' + task_id + '&check=' + is_checked;
      } else {
        var url = '/index.php?task_id=' + task_id + '&check=' + is_checked;
      }
      
      window.location = url;
    }
  });
}

flatpickr('#date', {
  enableTime: false,
  dateFormat: "Y-m-d",
  locale: "ru"
});
