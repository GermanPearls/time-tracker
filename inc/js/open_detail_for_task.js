//Open detail for task with option to confirm deletion
function open_detail_for_task(taskid) {
    ticket = encodeURIComponent(taskid);
    window.location.href = '/time-tracker/task-detail/?task-id=' + ticket;
}