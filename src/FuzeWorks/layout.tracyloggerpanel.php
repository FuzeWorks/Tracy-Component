<?php
/**
 * FuzeWorks Tracy Component.
 *
 * The FuzeWorks PHP FrameWork
 *
 * Copyright (C) 2013-2019 TechFuze
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @author    TechFuze
 * @copyright Copyright (c) 2013 - 2019, TechFuze. (http://techfuze.net)
 * @license   https://opensource.org/licenses/MIT MIT License
 *
 * @link  http://techfuze.net/fuzeworks
 * @since Version 1.2.0
 *
 * @version Version 1.2.0
 */
?>
<style class="tracy-debug">



</style>

<div class="fuzeworks-LoggerPanel">
<h1> Logger</h1>

<div class="tracy-inner">
	<table>
	<thead>
	<tr>
		<th>#</th>
		<th>Type</th>
		<th>Message</th>
		<th>File</th>
		<th>Line</th>
		<th>Timing</th>
	</tr>
	</thead>

	<tbody>
	<?php foreach ($logs as $key => $log): ?>
		<?php if ($log['type'] === 'LEVEL_STOP')
		{
			continue;
		}
		elseif ($log['type'] === 'LEVEL_START')
		{
			$log['type'] = 'CINFO';
		}
		?>
	<tr class="<?php echo($log['type']); ?>">
		<td><?php echo(  htmlspecialchars($key)); ?></td>
		<td><?php echo(  htmlspecialchars ($log['type'])); ?></td>
		<td><?php echo(  htmlspecialchars ($log['message'])); ?></td>
		<td><?php echo( empty($log['logFile']) ? 'x' : htmlspecialchars ($log['logFile'])); ?></td>
		<td><?php echo( empty($log['logLine']) ? 'x' : htmlspecialchars ($log['logLine'])); ?></td>
		<td><?php echo(round($log['runtime'] * 1000, 4)); ?> ms</td>
	</tr>
	<?php endforeach ?>
	</tbody>
	</table>
</div>
</div>
