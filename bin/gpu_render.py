import bpy
bpy.context.user_preferences.system.compute_device_type = 'CUDA'
bpy.context.user_preferences.system.compute_device = 'CUDA_0'
bpy.context.scene.cycles.device = 'GPU'