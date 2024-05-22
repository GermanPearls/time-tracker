//Open list of tasks for client
function open_task_list_for_client(clientName) {
    client = encodeURIComponent(clientName);
    window.location.href = scriptDetails.tthomeurl + '/task-list/?client=' + client;
}