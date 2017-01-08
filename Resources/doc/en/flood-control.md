# IP-address restriction

An attacker can begin to create hundreds of error requests per second that could
bring down the server (DoS - Denial of Service attack). To prevent this, need to
store user IP-address, and check when the user makes a request last time. If the
interval between the current and previous queries less than certain time 
(`query_interval` parameter), the user gets one violation, and the current 
request is dropped. If the user has multiple violations, it is blocked (banned).

The ability to restrict IP-addresses running as handling event 
`lexxpavlov_spelling.new_error`. See more in [Additional check of error](custom-event-listener.md)

## Parameters

Parameter        | Default value           | Description
-----------------|-------------------------|------------------------------------
query_interval   | 5 seconds               | Minimal interval between of queries
ban_count        | 10                      | Count of violations for ban
ban_period       | 86400 seconds (one day) | Period for which IP-address is blocked
ban_check_period | 86400 seconds (one day) | The interval in which the number of violations is considered for blocking IP-address. After a specified time, the number of violations is reset. 

> **Note.** If `query_interval` is 0, then check of IP-address is disabled.
