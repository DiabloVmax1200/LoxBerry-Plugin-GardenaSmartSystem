# Loxberry Plugin: Gardena-Smart-System
Dieses Plugin ermöglicht es Daten von einem Gardena-Smart-System an die Miniserver über UDP zu senden. 

Das Plugin unterstützt auch mehrere Geräte innerhalb eines Accounts. Jeder Messwert (Sensor) wird als einzelnes UDP Paket an den Miniserver gesendet. Das Paket hat immer folgende Aufbau:

[DeviceCategory].[DeviceName].[DataCategorie].[DataName]:[DataValue] (optional:[ = DataValueString])

Folgende Daten können gelesen werden:

Location:My Garden
authorized_at
address
latitude
longitude

Device Category:gateway

Categorie: device_info
manufacturer
product
serial_number
sgtin
version
category
last_time_online

Categorie: gateway
ip_address
time_zone
homekit_setup_payload

Device Category:mower

Categorie: device_info
manufacturer
product
serial_number
version
category
last_time_online
sgtin

Categorie: battery
level
charging
--->Possible Values: array ( 0 => 'true', 1 => 'false', )

Categorie: radio
quality
connection_status
--->Possible Values: array ( 0 => 'unknown', 1 => 'status_device_unreachable', 2 => 'status_device_alive', )
state:
--->Possible Values: array ( 0 => 'bad', 1 => 'poor', 2 => 'good', 3 => 'undefined', )

Categorie: firmware
firmware_status
firmware_upload_progress
firmware_available_version
inclusion_status
firmware_update_start
firmware_command
--->Possible Values: array ( 0 => 'idle', 1 => 'firmware_cancel', 2 => 'firmware_flash', 3 => 'firmware_upload', 4 => 'unsupported', )

Categorie: mower
manual_operation
status
--->Possible Values: array ( 0 => 'paused', 1 => 'ok_cutting', 2 => 'ok_searching', 3 => 'ok_charging', 4 => 'ok_leaving', 5 => 'wait_updating', 6 => 'wait_power_up', 7 => 'parked_timer', 8 => 'parked_park_selected', 9 => 'off_disabled', 10 => 'off_hatch_open', 11 => 'unknown', 12 => 'error', 13 => 'error_at_power_up', 14 => 'off_hatch_closed', 15 => 'ok_cutting_timer_overridden', 16 => 'parked_autotimer', 17 => 'parked_daily_limit_reached', 18 => 'undefined', )
error
--->Possible Values: array ( 0 => 'no_message', 1 => 'outside_working_area', 2 => 'no_loop_signal', 3 => 'wrong_loop_signal', 4 => 'loop_sensor_problem_front', 5 => 'loop_sensor_problem_rear', 6 => 'loop_sensor_problem_left', 7 => 'loop_sensor_problem_right', 8 => 'wrong_pin_code', 9 => 'trapped', 10 => 'upside_down', 11 => 'low_battery', 12 => 'empty_battery', 13 => 'no_drive', 14 => 'temporarily_lifted', 15 => 'lifted', 16 => 'stuck_in_charging_station', 17 => 'charging_station_blocked', 18 => 'collision_sensor_problem_rear', 19 => 'collision_sensor_problem_front', 20 => 'wheel_motor_blocked_right', 21 => 'wheel_motor_blocked_left', 22 => 'wheel_drive_problem_right', 23 => 'wheel_drive_problem_left', 24 => 'cutting_motor_drive_defect', 25 => 'cutting_system_blocked', 26 => 'invalid_sub_device_combination', 27 => 'settings_restored', 28 => 'memory_circuit_problem', 29 => 'slope_too_steep', 30 => 'charging_system_problem', 31 => 'stop_button_problem', 32 => 'tilt_sensor_problem', 33 => 'mower_tilted', 34 => 'wheel_motor_overloaded_right', 35 => 'wheel_motor_overloaded_left', 36 => 'charging_current_too_high', 37 => 'electronic_problem', 38 => 'cutting_motor_problem', 39 => 'limited_cutting_height_range', 40 => 'unexpected_cutting_height_adj', 41 => 'cutting_height_problem_drive', 42 => 'cutting_height_problem_curr', 43 => 'cutting_height_problem_dir', 44 => 'cutting_height_blocked', 45 => 'cutting_height_problem', 46 => 'no_response_from_charger', 47 => 'ultrasonic_problem', 48 => 'temporary_problem', 49 => 'guide_1_not_found', 50 => 'guide_2_not_found', 51 => 'guide_3_not_found', 52 => 'gps_tracker_module_error', 53 => 'weak_gps_signal', 54 => 'difficult_finding_home', 55 => 'guide_calibration_accomplished', 56 => 'guide_calibration_failed', 57 => 'temporary_battery_problem', 58 => 'battery_problem', 59 => 'too_many_batteries', 60 => 'alarm_mower_switched_off', 61 => 'alarm_mower_stopped', 62 => 'alarm_mower_lifted', 63 => 'alarm_mower_tilted', 64 => 'alarm_mower_in_motion', 65 => 'alarm_outside_geofence', 66 => 'connection_changed', 67 => 'connection_not_changed', 68 => 'com_board_not_available', 69 => 'slipped', 70 => 'invalid_battery_combination', 71 => 'imbalanced_cutting_disc', 72 => 'safety_function_faulty', )
last_error_code
source_for_next_start
--->Possible Values: array ( 0 => 'no_source', 1 => 'completed_cutting_daily_limit', 2 => 'week_timer', 3 => 'countdown_timer', 4 => 'mower_charging', 5 => 'completed_cutting_autotimer', 6 => 'undefined', )
timestamp_next_start
override_end_time
timestamp_last_error_code

Categorie: mower_stats
cutting_time
timestamp
charging_cycles
collisions
running_time

Categorie: mower_type
base_software_up_to_date
mmi_version
mainboard_version
comboard_version
device_type
device_variant


Device Category:sensor
Categorie: device_info
manufacturer
product
serial_number
version
category
last_time_online
sgtin

Categorie: battery
level
disposable_battery_status
--->Possible Values: array ( 0 => 'out_of_operation', 1 => 'replace_now', 2 => 'low', 3 => 'ok', 4 => 'undefined', )

Categorie: radio
quality
connection_status:
--->Possible Values: array ( 0 => 'unknown', 1 => 'status_device_unreachable', 2 => 'status_device_alive', )
state
--->Possible Values: array ( 0 => 'bad', 1 => 'poor', 2 => 'good', 3 => 'undefined', )

Categorie: ambient_temperature
temperature
frost_warning
--->Possible Values: array ( 0 => 'no_frost', 1 => 'frost', 2 => 'undefined', )

Categorie: soil_temperature
temperature

Categorie: humidity
humidity

Categorie: light
light

Categorie: identification

Categorie: firmware
firmware_status
firmware_upload_progress
firmware_available_version
inclusion_status
firmware_update_start
firmware_command
--->Possible Values: array ( 0 => 'idle', 1 => 'firmware_cancel', 2 => 'firmware_flash', 3 => 'firmware_upload', 4 => 'unsupported', )

## Feedback und Diskussion
Das PlugIn wird von mir noch weiterentwickelt und ich freue mich über Anregungen und Feedback. 

## Change-Log
- 2018-07-12  Erstellung PlugIn v 0.0.5

## Known-Issues
- Logging erfolgt nicht korrekt
- Noch keine Selektion der zu versendenten Werte möglich
- Noch keine anständige Dokumentation


## Sensor-Werte
todo