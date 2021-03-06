<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\tests\unit\http\routing\traits;

use mako\http\Request;
use mako\http\request\Files;
use mako\http\request\Parameters;
use mako\http\routing\traits\InputValidationTrait;
use mako\tests\TestCase;
use Mockery;

/**
 * @group unit
 */
class InputValidationTraitTest extends TestCase
{
	/**
	 *
	 */
	public function testGetValidatedInputWithArray(): void
	{
		$request = Mockery::mock(Request::class);

		$parameters = Mockery::mock(Parameters::class);

		$request->shouldReceive('getData')->once()->andReturn($parameters);

		$parameters->shouldReceive('all')->andReturn([]);

		$class = new class
		{
			use InputValidationTrait;

			public function test(): array
			{
				return $this->getValidatedInput(['foo' => ['required']]);
			}

			public function baseGetValidatedInput(...$arguments): array
			{
				return $arguments;
			}
		};

		$class->request = $request;

		$this->assertSame([[], ['foo' => ['required']]], $class->test());
	}

	/**
	 *
	 */
	public function testGetValidatedInputWithString(): void
	{
		$request = Mockery::mock(Request::class);

		$request->shouldReceive('getData')->never();

		$class = new class
		{
			use InputValidationTrait;

			public function test(): array
			{
				return $this->getValidatedInput('Foo');
			}

			public function baseGetValidatedInput(...$arguments): array
			{
				return $arguments;
			}
		};

		$class->request = $request;

		$this->assertSame(['Foo'], $class->test());
	}

	/**
	 *
	 */
	public function testGetValidatedFilesWithArray(): void
	{
		$request = Mockery::mock(Request::class);

		$files = Mockery::mock(Files::class);

		$request->shouldReceive('getFiles')->once()->andReturn($files);

		$files->shouldReceive('all')->andReturn([]);

		$class = new class
		{
			use InputValidationTrait;

			public function test(): array
			{
				return $this->getValidatedFiles(['foo' => ['required']]);
			}

			public function baseGetValidatedInput(...$arguments): array
			{
				return $arguments;
			}
		};

		$class->request = $request;

		$this->assertSame([[], ['foo' => ['required']]], $class->test());
	}

	/**
	 *
	 */
	public function testGetValidatedFilesWithString(): void
	{
		$request = Mockery::mock(Request::class);

		$request->shouldReceive('getData')->never();

		$class = new class
		{
			use InputValidationTrait;

			public function test(): array
			{
				return $this->getValidatedFiles('Foo');
			}

			public function baseGetValidatedInput(...$arguments): array
			{
				return $arguments;
			}
		};

		$class->request = $request;

		$this->assertSame(['Foo'], $class->test());
	}
}
