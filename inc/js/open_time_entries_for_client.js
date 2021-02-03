//Open list of time entries for client
function open_time_entries_for_client(clientName) {
    client = encodeURIComponent(clientName);
    window.location.href = '/time-tracker/time-log/?client=' + client;
}