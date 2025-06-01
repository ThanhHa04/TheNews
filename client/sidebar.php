<div class="sidebar">   
    <h3>📚 Khối - Lớp</h3>
    <?php
        $grades = ['10', '11', '12'];
        foreach ($grades as $grade) {
            echo "<div class='grade-block'>";
            echo "<button class='toggle-btn' type='button' onclick='toggleClasses(\"$grade\")'>Khối $grade</button>";
            echo "<div id='classes-$grade' class='classes' style='display:none'>";
            foreach (['A', 'B', 'C'] as $suffix) {
                $class = $grade . $suffix;
                echo 
                "<form method='get' action='class.php' style='display:inline'>
                    <input type='hidden' name='class' value='" . htmlspecialchars($class) . "'>
                    <button type='submit'>" . htmlspecialchars($class) . "</button>
                </form>";
            }
            echo "</div></div>";
        }
        ?>
</div>

<script>
    function toggleClasses(grade) {
        const el = document.getElementById(`classes-${grade}`);
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
</script>