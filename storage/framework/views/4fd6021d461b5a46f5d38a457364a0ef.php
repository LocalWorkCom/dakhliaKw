<?php $__env->startSection('content'); ?>
<div class="container">
    <h2>اضافة قسم جديد</h2>
    <form action="<?php echo e(route('sub_departments.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        
        <div class="form-group">
            <label for="name">اسم القسم </label>
            <input type="text" class="form-control" id="name" name="name" >
        </div>
        <div class="form-group">
            <select name="parent_id" id="parent_id" class="form-control">
            <option value="" <?php echo e(is_null($parentDepartment) ? 'selected' : ''); ?>>اختار القسم</option>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($department->parent_id); ?>">
                        <?php echo e($department->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">اضافة</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dakhliaKw\resources\views/sub_departments/create.blade.php ENDPATH**/ ?>