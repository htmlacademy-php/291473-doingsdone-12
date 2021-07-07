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
      var task_id = el.getAttribute('value');
      var project_id = el.dataset.project;
      var show_complete_tasks = el.dataset.completed;

      if (project_id && task_id && show_complete_tasks) {
        var url = '/index.php?project-id=' + project_id + '&task_id=' + task_id + '&check=' + is_checked + '&show_completed=' + show_complete_tasks;
      } else if (project_id && task_id) {
        var url = '/index.php?project-id=' + project_id + '&task_id=' + task_id + '&check=' + is_checked;
      } else if (task_id && show_complete_tasks) {
        var url = '/index.php?task_id=' + task_id + '&check=' + is_checked + '&show_completed=' + show_complete_tasks;
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
