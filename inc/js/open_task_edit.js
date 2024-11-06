//Open detail for task with editable fields
function open_task_edit_screen(taskid) {
    ticket = encodeURIComponent(taskid);
    window.location.href = scriptDetails.tthomeurl + '/task-edit/?task-id=' + ticket;
}